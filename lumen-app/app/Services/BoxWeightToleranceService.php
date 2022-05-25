<?php

namespace App\Services;

use App\Api\BoxTypeServiceInterface;

class BoxWeightToleranceService implements \App\Api\BoxWeightToleranceServiceInterface
{
    protected BoxTypeServiceInterface $boxTypeService;
    /**
     * @var int[]
     */
    private array $boxWeightToleranceByBoxType;

    /**
     * @param BoxTypeServiceInterface $boxTypeService
     */
    public function __construct(BoxTypeServiceInterface $boxTypeService)
    {
        $this->boxTypeService = $boxTypeService;
    }

    /**
     * returns configured box weight tolerance for each box type
     * @return int[]
     */
    protected function getBoxWeightTolerance(): array
    {
        if (!isset($this->boxWeightToleranceByBoxType)) {
            $this->boxWeightToleranceByBoxType = config('app.box.weight_tolerance', []);
        }
        return $this->boxWeightToleranceByBoxType;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getBoxWeightTolerancePercent(int $expectedWeight, int $boxUID): float
    {
        $boxType = $this->boxTypeService->getBoxType($boxUID);
        if (!array_key_exists($boxType, $this->getBoxWeightTolerance())) {
            throw new \Exception("Box weight tolerance for box type $boxType is not set in app config");
        }
        return abs(round((float)(($this->getBoxWeightTolerance()[$boxType] / $expectedWeight) * 100), 1));
    }
}