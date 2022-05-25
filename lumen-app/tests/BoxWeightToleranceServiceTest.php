<?php

use Illuminate\Support\Facades\Config;

class BoxWeightToleranceServiceTest extends TestCase
{
    protected $boxWeightToleranceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boxWeightToleranceService = new \App\Services\BoxWeightToleranceService(
            new \App\Services\BoxTypeService()
        );
    }

    public function testGetBoxWeightTolerancePercentNoConfig()
    {
        Config::set('app.box.weight_tolerance', []);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Box weight tolerance for box type 1111 is not set in app config");
        $this->boxWeightToleranceService->getBoxWeightTolerancePercent(1000, 11111365);
    }

    public function testGetBoxWeightTolerancePercentWrongConfig()
    {
        Config::set('app.box.weight_tolerance', [111 => 10]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Box weight tolerance for box type 1111 is not set in app config");
        $this->boxWeightToleranceService->getBoxWeightTolerancePercent(1000, 11111365);
    }

    public function testGetBoxWeightTolerancePercentZeroInConfig()
    {
        Config::set('app.box.weight_tolerance', [1111 => 0]);
        $this->assertSame(
            0.0,
            $this->boxWeightToleranceService->getBoxWeightTolerancePercent(1000, 11111365)
        );
    }

    public function testGetBoxWeightTolerancePercentNegativeInConfig()
    {
        Config::set('app.box.weight_tolerance', [1111 => -10]);
        $this->assertSame(
            1.0,
            $this->boxWeightToleranceService->getBoxWeightTolerancePercent(1000, 11111365)
        );
    }

    public function testGetBoxWeightTolerancePercentRegularNumber()
    {
        Config::set('app.box.weight_tolerance', [1111 => 210]);
        $this->assertSame(
            21.0,
            $this->boxWeightToleranceService->getBoxWeightTolerancePercent(1000, 11111365)
        );
    }

    public function testGetBoxWeightTolerancePercentBiggerNumber()
    {
        Config::set('app.box.weight_tolerance', [1111 => 10]);
        $this->assertSame(
            0.1,
            $this->boxWeightToleranceService->getBoxWeightTolerancePercent(12000, 11111365)
        );
    }
}