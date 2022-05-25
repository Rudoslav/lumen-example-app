<?php

namespace App\Models;

use App\Api\ExpectedWeightInterface;

class ExpectedWeightFactory
{
    public function create(int $id, int $orderId, int $weightExpected, string $createdAt, string $updatedAt): ExpectedWeightInterface
    {
        return new ExpectedWeight($id, $orderId, $weightExpected, $createdAt, $updatedAt);
    }
}