<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */

use App\Services\WeightToleranceService;
use Mockery\MockInterface;
use Illuminate\Support\Facades\App;

class WeightToleranceServiceTest extends TestCase
{
    protected WeightToleranceService $weightToleranceService;

    protected function setup(): void
    {
        parent::setUp();
        $this->weightToleranceService = new WeightToleranceService(
            new \App\Services\BoxWeightToleranceService(
                new \App\Services\BoxTypeService()
            )
        );
    }

    public function testIsWeightAcceptedRelativeCorrect()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 15, 'type' => 'relative'];

        $mock = Mockery::mock('App\Services\WeightToleranceService')->shouldAllowMockingProtectedMethods();
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $mock->makePartial()->shouldReceive('getBoxWeightTolerancePercent')
            ->once()
            ->andReturn(2.0);

        $this->assertTrue(
            $mock->isWeightAccepted($expectedWeight, $realWeight, 11111365),
            'Real weight is in given relative tolerance'
        );
    }

    public function testIsWeightAcceptedRelativeHighBoxWeightToleranceCorrect()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 5, 'type' => 'relative'];

        $mock = Mockery::mock('App\Services\WeightToleranceService')->shouldAllowMockingProtectedMethods();
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $mock->makePartial()->shouldReceive('getBoxWeightTolerancePercent')
            ->once()
            ->andReturn(8.0);

        $this->assertTrue(
            $mock->isWeightAccepted($expectedWeight, $realWeight, 11111365),
            'Real weight is in given relative tolerance'
        );
    }

    public function testIsWeightAcceptedRelativeWrong()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 7, 'type' => 'relative'];

        $mock = Mockery::mock('App\Services\WeightToleranceService')->shouldAllowMockingProtectedMethods();
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $mock->makePartial()->shouldReceive('getBoxWeightTolerancePercent')
            ->once()
            ->andReturn(1.0);

        $this->assertFalse(
            $mock->isWeightAccepted($expectedWeight, $realWeight, 11111365),
            'Real weight is out of given relative tolerance'
        );
    }

    public function testIsWeightAcceptedAbsoluteCorrect()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 100, 'type' => 'absolute'];

        $mock = Mockery::mock('App\Services\WeightToleranceService');
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $this->assertTrue($mock->isWeightAccepted($expectedWeight, $realWeight), 'Real weight is in given absolute tolerance');
    }

    public function testIsWeightAcceptedAbsoluteWrong()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 90, 'type' => 'absolute'];

        $mock = Mockery::mock('App\Services\WeightToleranceService');
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $this->assertFalse($mock->isWeightAccepted($expectedWeight, $realWeight), 'Real weight is out of given absolute tolerance');
    }

    public function testIsWeightAcceptedRelativeZeroBoxWeightToleranceCorrect()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 15, 'type' => 'relative'];

        $mock = Mockery::mock('App\Services\WeightToleranceService')->shouldAllowMockingProtectedMethods();
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $mock->makePartial()->shouldReceive('getBoxWeightTolerancePercent')
            ->once()
            ->andReturn(0.0);

        $this->assertTrue(
            $mock->isWeightAccepted($expectedWeight, $realWeight, 11111365),
            'Real weight is in given relative tolerance'
        );
    }

    public function testIsWeightAcceptedRelativeZeroBoxWeightToleranceWrong()
    {
        $expectedWeight = 1000;
        $realWeight = 1100;
        $tolerance = ['value' => 7, 'type' => 'relative'];

        $mock = Mockery::mock('App\Services\WeightToleranceService')->shouldAllowMockingProtectedMethods();
        $mock->makePartial()->shouldReceive('getTolerance')->once()->andReturn($tolerance);
        $mock->makePartial()->shouldReceive('getBoxWeightTolerancePercent')
            ->once()
            ->andReturn(0.0);

        $this->assertFalse(
            $mock->isWeightAccepted($expectedWeight, $realWeight, 11111365),
            'Real weight is out of given relative tolerance'
        );
    }

    public function testIsWeightAcceptedZeroWeight()
    {
        $expectedWeight = 0;
        $realWeight = 0;

        $mock = Mockery::mock('App\Services\WeightToleranceService');
        $mock->makePartial()->shouldReceive('getTolerance')->never();
        $this->expectException(\Exception::class);
        $mock->isWeightAccepted($expectedWeight, $realWeight);
    }

    public function testSetToleranceCorrectData()
    {
        $value = 10;
        $type = "relative";

        $this->assertEquals($this->weightToleranceService->setTolerance($value, $type), ['value' => 10, 'type' => 'relative']);
    }

    public function testSetToleranceWrongData()
    {
        $value = null;
        $type = "relativea";

        $this->expectException(\Exception::class);
        $this->weightToleranceService->setTolerance($value, $type);
    }

    public function testSetToleranceCorrectEnvData()
    {
        $this->assertArrayHasKey('value', $this->weightToleranceService->setTolerance());
        $this->assertArrayHasKey('type', $this->weightToleranceService->setTolerance());
        $this->assertIsInt($this->weightToleranceService->setTolerance()['value']);
        $this->assertMatchesRegularExpression('/^absolute|relative$/i', $this->weightToleranceService->setTolerance()['type']);
    }
}
