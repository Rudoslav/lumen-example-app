<?php

namespace App\Api;

interface ExpectedWeightInterface
{
    public function getId(): int;
    public function getOrderId(): int;
    public function getWeightExpected(): int;
    public function getCreatedAt(): string;
    public function getUpdatedAt(): string;
}