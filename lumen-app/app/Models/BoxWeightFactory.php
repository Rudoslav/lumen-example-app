<?php

namespace App\Models;

use App\Api\BoxWeightInterface;

class BoxWeightFactory
{
    public function create(
        int $weightExpected,
        ?int $weightReal,
        int $orderId,
        int $messageNumber,
        int $boxUID,
        int $measuredAt,
        int $pickerId,
        string $weightExpectedUnit,
        string $weightRealUnit
    ): BoxWeightInterface
    {
        $boxWeight = new BoxWeight();
        $boxWeight->setWeightExpected($weightExpected);
        $boxWeight->setOrderId($orderId);
        $boxWeight->setMessageNumber($messageNumber);
        $boxWeight->setBoxUID($boxUID);
        $boxWeight->setMeasuredAt($measuredAt);
        $boxWeight->setPickerId($pickerId);
        $boxWeight->setWeightExpectedUnit($weightExpectedUnit);
        $boxWeight->setWeightReal($weightReal);
        $boxWeight->setWeightRealUnit($weightRealUnit);
        return $boxWeight;
    }
}