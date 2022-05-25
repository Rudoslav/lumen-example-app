<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */
declare(strict_types=1);

namespace App\Models;

use Illuminate\Http\Client\Response;
use App\Api\BoxWeightRepositoryInterface;
use App\Exceptions\Service\ApiErrorResponseException;
use App\Api\RealWeightResponseProcessorInterface;
use App\Models\Logger\Logger;

class RealWeightResponseProcessor implements RealWeightResponseProcessorInterface
{
    public const HTTP_OK = 200;

    protected BoxWeightRepositoryInterface $boxWeightRepository;
    protected Logger $logger;

    public function __construct(
        BoxWeightRepositoryInterface $boxWeightRepository,
        Logger $logger
    ) {
        $this->boxWeightRepository = $boxWeightRepository;
        $this->logger = $logger;
    }

    /**
     * @param Response $response
     * @throws ApiErrorResponseException
     */
    public function processResponse(Response $response): void
    {
        switch ($response->status()) {
            case self::HTTP_OK:
                $this->setWeightRealSyncAt($response->object());
                break;
            default:
                throw new ApiErrorResponseException($response->body());
        }
    }

    /**
     * @param \stdClass $responseData
     */
    public function setWeightRealSyncAt(\stdClass $responseData): void
    {
        $succes = 0;
        $errors = 0;

        /** @var string $boxWeight */
        foreach ($responseData->data as $boxWeight) {
            try {
                $boxWeightObj = json_decode($boxWeight);
                if ($boxWeightObj->status !== 200) {
                    $this->logger->logError(
                        __CLASS__.': Response from Magento for order '.$boxWeightObj->resource->order_id
                        .': ' . $boxWeightObj->message
                    );
                    $errors++;
                    continue;
                }
                $this->updateSyncAt($boxWeightObj->resource->order_id);
                $succes++;
            } catch (\Exception $exception) {
                $this->logger->logError('Error UpdateSyncAt: ' . $exception->getMessage());
                $errors++;
                continue;
            }
        }
        $this->logger->logInfo(
            ($succes + $errors) . "BoxWeight data was synchronized to Magento. ({$succes}) success, ({$errors}) errors"
        );
    }

    /**
     * @param int $orderId
     * @throws \App\Exceptions\Model\CouldNotSaveException
     */
    private function updateSyncAt(int $orderId): void
    {
        $boxWeight = $this->boxWeightRepository->getByOrderId($orderId);
        if (is_null($boxWeight) || !$boxWeight->getId()) {
            throw new \Exception("BoxWeight entity with ID: {$orderId} does not exists");
        }
        $boxWeight->setWeightRealSyncAt(date('Y-m-d H:i:s'));
        $this->boxWeightRepository->save($boxWeight);
    }
}
