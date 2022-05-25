<?php

namespace App\Api;

interface BoxWeightToleranceServiceInterface
{
    /**
     * returns percentage of box's weight tolerance for specific weight and box
     * @param int $expectedWeight
     * @param int $boxUID
     * @return float
     */
    public function getBoxWeightTolerancePercent(int $expectedWeight, int $boxUID): float;
}