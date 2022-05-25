<?php

namespace App\Models;

use App\Api\ExpectedWeightInterface;

class ExpectedWeight implements ExpectedWeightInterface
{
    protected int $id;
    protected int $orderId;
    protected int $weightExpected;
    protected string $createdAt;
    protected string $updatedAt;

    public function __construct(int $id, int $orderId, int $weightExpected, string $createdAt, string $updatedAt)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->weightExpected = $weightExpected;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getWeightExpected(): int
    {
        return $this->weightExpected;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}