<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */

use App\Exceptions\Service\ApiErrorResponseException;
use App\Models\Logger\Logger;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Repositories\BoxWeightRepository;
use App\Console\Commands\SendRealWeightToMagento;
use App\Models\RealWeightResponseProcessor;
use App\Models\BoxWeight;

class SendRealWeightToMagentoTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testGetBoxWeightCollection()
    {
        $responseProcessorMock = Mockery::mock(\App\Models\RealWeightResponseProcessor::class);
        $serviceMock = Mockery::mock(\App\Services\RealWeightService::class);
        $loggerMock = Mockery::mock(\App\Models\Logger\Logger::class);
        $boxWeight = new BoxWeight();

        $sendRealWeightToMagento = new SendRealWeightToMagento($boxWeight, $serviceMock, $responseProcessorMock, $loggerMock);
        $data = $sendRealWeightToMagento->getBoxWeightCollection();
        $this->assertTrue($data->count() == 2);
    }
}
