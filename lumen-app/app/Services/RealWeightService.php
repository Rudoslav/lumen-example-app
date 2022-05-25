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

namespace App\Services;

use App\Api\RealWeightServiceInterface;
use App\Exceptions\Service\ApiErrorConfigException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class RealWeightService implements RealWeightServiceInterface
{

    /**
     * @param Collection $boxWeightCollection
     * @return Response
     * @throws ApiErrorConfigException
     */
    public function post(Collection $boxWeightCollection): Response
    {
        try {
            $response = Http::withToken($this->getAuthToken())
                ->post($this->getApiEndpoint(), $this->getData($boxWeightCollection));
        } catch (\Exception $exception) {
            throw new ApiErrorConfigException($exception->getMessage());
        }
        return $response;
    }

    /**
     * @return string
     * @throws ApiErrorConfigException
     */
    private function getApiEndpoint(): string
    {
        $url = env('APP_WEIGHT_REAL_SYNC_URL');
        if (!$url) {
            throw new ApiErrorConfigException('Api endpoint mismatch');
        }
        return $url;
    }

    /**
     * @return string
     * @throws ApiErrorConfigException
     */
    private function getAuthToken(): string
    {
        $token = env('APP_WEIGHT_REAL_SYNC_AUTH_TOKEN');
        if (!$token) {
            throw new ApiErrorConfigException('Api token is missing');
        }
        return $token;
    }

    /**
     * @param Collection $boxWeightCollection
     * @return array
     */
    private function getData(Collection $boxWeightCollection): array
    {
        return [
            'data' => $boxWeightCollection->toArray()
        ];
    }
}
