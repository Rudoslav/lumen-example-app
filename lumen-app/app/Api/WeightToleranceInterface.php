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

namespace App\Api;

interface WeightToleranceInterface
{
    /**
     * @param int $expectedWeight
     * @param int $realWeight
     * @param int|null $boxUID
     * @return bool
     */
    public function isWeightAccepted(int $expectedWeight, int $realWeight, ?int $boxUID = null): bool;
}
