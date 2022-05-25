<?php

namespace App\Services;

use App\Api\BoxWeightInterface;
use App\Api\BoxWeightRepositoryInterface;

class BoxWeightService
{
    protected BoxWeightRepositoryInterface $boxWeightRepository;

    /**
     * @param BoxWeightRepositoryInterface $boxWeightRepository
     */
    public function __construct(BoxWeightRepositoryInterface $boxWeightRepository)
    {
        $this->boxWeightRepository = $boxWeightRepository;
    }

    /**
     * @param int $boxUID
     * @return BoxWeightInterface|null
     */
    public function findByBoxUID(int $boxUID): ?BoxWeightInterface
    {
        return $this->boxWeightRepository->getByBoxUID($boxUID);
    }

    /**
     * @param int $orderId
     * @return BoxWeightInterface|null
     */
    public function findByOrderID(int $orderId): ?BoxWeightInterface
    {
        return $this->boxWeightRepository->getByOrderId($orderId);
    }

    /**
     * @param BoxWeightInterface $boxWeight
     * @throws \App\Exceptions\Model\CouldNotSaveException
     */
    public function save(BoxWeightInterface $boxWeight)
    {
        $this->boxWeightRepository->save($boxWeight);
    }
}