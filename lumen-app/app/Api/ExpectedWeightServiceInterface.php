<?php

namespace App\Api;

interface ExpectedWeightServiceInterface
{
    /**
     * @param int $numOfWeights
     * @return ExpectedWeightInterface[]
     */
    public function get(int $numOfWeights): array;
}