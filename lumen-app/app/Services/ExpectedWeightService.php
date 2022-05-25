<?php

namespace App\Services;

use App\Api\ExpectedWeightServiceInterface;
use App\Exceptions\Service\ApiErrorResponseException;
use App\Exceptions\Service\MissingExpectedWeightSyncTokenException;
use App\Exceptions\Service\MissingExpectedWeightSyncUrlException;
use App\Models\ExpectedWeight;
use App\Models\ExpectedWeightFactory;
use Illuminate\Support\Facades\Http;

class ExpectedWeightService implements ExpectedWeightServiceInterface
{
    protected const REQUIRED_RESPONSE_PROPERTIES = ['id', 'order_id', 'weight_expected', 'created_at', 'updated_at'];

    protected LogService $logService;
    protected ExpectedWeightFactory $expectedWeightFactory;

    /**
     * @param LogService $logService
     * @param ExpectedWeightFactory $expectedWeightFactory
     */
    public function __construct(LogService $logService, ExpectedWeightFactory $expectedWeightFactory)
    {
        $this->logService = $logService;
        $this->expectedWeightFactory = $expectedWeightFactory;
    }

    /**
     * @param int $numOfWeights
     * @return ExpectedWeight[]
     * @throws ApiErrorResponseException
     * @throws MissingExpectedWeightSyncTokenException
     * @throws MissingExpectedWeightSyncUrlException
     */
    public function get(int $numOfWeights): array
    {
        $authToken = env('APP_BOX_WEIGHT_SYNC_AUTH_TOKEN', '');
        $expectedWeightUrl = env('APP_BOX_WEIGHT_SYNC_URL', '');
        if (!$authToken) {
            throw new MissingExpectedWeightSyncTokenException();
        }
        if (!$expectedWeightUrl) {
            throw new MissingExpectedWeightSyncUrlException();
        }
        $this->logService->log(__CLASS__, LogService::LEVEL_INFO, "calling api $expectedWeightUrl");
        $response = Http::withToken($authToken)->get($expectedWeightUrl);
        $this->logService->log(
            __CLASS__,
            LogService::LEVEL_INFO,
            'received response with status: '.$response->status()
        );
        $this->checkResponse($response);
        return array_slice($this->decodeResponse($response), 0, $numOfWeights);
    }

    /**
     * @param \Illuminate\Http\Client\Response $response
     * @throws ApiErrorResponseException
     */
    public function checkResponse(\Illuminate\Http\Client\Response $response)
    {
        if ($response->status() !== 200) {
            throw new ApiErrorResponseException("HTTP status code: ".$response->status());
        }
    }

    /**
     * @param \Illuminate\Http\Client\Response $response
     * @return ExpectedWeight[]
     * @throws ApiErrorResponseException
     */
    public function decodeResponse(\Illuminate\Http\Client\Response $response): array
    {
        $responseBody = $response->body();
        $responseBodyDecoded = json_decode($responseBody, true);
        if (!$responseBody || !is_array($responseBodyDecoded)) {
            throw new ApiErrorResponseException("Response body is empty or could not be decoded");
        }
        $result = [];
        foreach ($responseBodyDecoded as $expectedWeight) {
            if (!is_array($expectedWeight)) {
                $this->logService->log(
                    __CLASS__,
                    LogService::LEVEL_WARNING,
                    'found expected weight invalid format in response, skipping it...',
                    ['problematic_weight' => $expectedWeight]
                );
                continue;
            }
            foreach (self::REQUIRED_RESPONSE_PROPERTIES as $attribute) {
                if (!isset($expectedWeight[$attribute])) {
                    $this->logService->log(
                        __CLASS__,
                        LogService::LEVEL_WARNING,
                        "found expected weight with missing attribute '$attribute' in response, skipping it...",
                        ['problematic_weight' => $expectedWeight]
                    );
                    continue 2;
                }
            }
            $result[] = $this->expectedWeightFactory->create(
                (int)$expectedWeight['id'],
                (int)$expectedWeight['order_id'],
                (int)$expectedWeight['weight_expected'],
                (string)$expectedWeight['created_at'],
                (string)$expectedWeight['updated_at'],
            );
        }
        return $result;
    }
}