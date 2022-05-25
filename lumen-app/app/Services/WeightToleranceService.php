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

use App\Api\BoxWeightToleranceServiceInterface;
use App\Api\WeightToleranceInterface;

class WeightToleranceService implements WeightToleranceInterface
{
    protected BoxWeightToleranceServiceInterface $boxWeightToleranceService;
    private array $tolerance;

    /**
     * @param BoxWeightToleranceServiceInterface $boxWeightToleranceService
     */
    public function __construct(BoxWeightToleranceServiceInterface $boxWeightToleranceService)
    {
        $this->boxWeightToleranceService = $boxWeightToleranceService;
    }

    /**
     * Test if weight is in tolerance
     *
     * @param int $expectedWeight
     * @param int $realWeight
     * @param int|null $boxUID
     * @return bool
     * @throws \Exception
     */
    public function isWeightAccepted(int $expectedWeight, int $realWeight, ?int $boxUID = null): bool
    {
        if ($expectedWeight === 0 || $realWeight === 0) {
            throw new \Exception(
                'Zero weight of the package or bad gravitation on the place (in 2nd case contact prof. Hawking)'
            );
        }
        $tolerance = $this->getTolerance();
        switch ($tolerance['type']) {
            case 'absolute':
                $delta = abs($expectedWeight - $realWeight);
                break;
            case 'relative':
                if (!$boxUID) {
                    throw new \InvalidArgumentException("boxUID argument is required with relative tolerance");
                }
                $delta = (abs($expectedWeight - $realWeight) / $expectedWeight) * 100;
                $tolerance['value'] += $this->getBoxWeightTolerancePercent($expectedWeight, $boxUID);
                break;
            default:
                throw new \Exception('Configuration data mismatch');
        }
        return $delta <= $tolerance['value'];
    }

    /**
     * Return tolerance
     *
     * @return array
     * @throws \Exception
     */
    public function getTolerance(): array
    {
        if (!isset($this->tolerance)) {
            $this->tolerance = $this->setTolerance();
        }
        return $this->tolerance;
    }

    /**
     * Set tolerance from arguments or .env file
     *
     * @param int|null $value
     * @param string|null $type
     * @return array
     * @throws \Exception
     */
    public function setTolerance(?int $value = null, ?string $type = null): array
    {
        $this->tolerance = [
            'value' => $value ?: (int)env('APP_WEIGHT_TOLERANCE_VALUE'),
            'type' => $type ?: (string)env('APP_WEIGHT_TOLERANCE_TYPE')
        ];
        if (!(isset($this->tolerance['value'])
                && isset($this->tolerance['type'])
                && $this->tolerance['value']
                && preg_match('/^absolute|relative$/i', $this->tolerance['type']))) {
            throw new \Exception('Invalid tolerance configuration data');
        }
        return $this->tolerance;
    }

    /**
     * @param int $expectedWeight
     * @param int $boxUID
     * @return float
     */
    protected function getBoxWeightTolerancePercent(int $expectedWeight, int $boxUID): float
    {
        return $this->boxWeightToleranceService->getBoxWeightTolerancePercent($expectedWeight, $boxUID);
    }
}
