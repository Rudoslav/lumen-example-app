<?php

namespace App\Api;

use App\Exceptions\Model\CouldNotSaveException;

interface BoxWeightRepositoryInterface
{
    /**
     * @param int $id
     * @return BoxWeightInterface|NULL
     */
    public function get(int $id): ?BoxWeightInterface;

    /**
     * @param int $orderId
     * @return BoxWeightInterface|NULL
     */
    public function getByOrderId(int $orderId): ?BoxWeightInterface;

    /**
     * @param int $boxUID
     * @return BoxWeightInterface|NULL
     */
    public function getByBoxUID(int $boxUID): ?BoxWeightInterface;

    /**
     * @throws CouldNotSaveException
     * @param BoxWeightInterface $boxWeight
     * @return mixed
     */
    public function save(BoxWeightInterface $boxWeight);
}