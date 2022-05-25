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
use App\Models\RealWeightResponseProcessor;
use App\Models\Logger\Logger;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Repositories\BoxWeightRepository;

class RealWeightResponseProcessorTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testProcessResponseHttpError()
    {
        $statusCode = 500;
        $responseMock = Mockery::mock(\Illuminate\Http\Client\Response::class);
        $realWeightResponseProcessor = new RealWeightResponseProcessor(
            new BoxWeightRepository(),
            new Logger()
        );

        $responseMock->shouldReceive('status')->once()->andReturn($statusCode);
        $responseMock->shouldReceive('body')->once()->andReturn('Bad Request');
        $this->expectException(ApiErrorResponseException::class);
        $realWeightResponseProcessor->processResponse($responseMock);
    }

    public function testProcessResponseHttpOk()
    {
        $statusCode = 200;
        $mockedOject = new stdClass();
        $responseMock = Mockery::mock(\Illuminate\Http\Client\Response::class);
        $responseMock->shouldReceive('status')
            ->once()->andReturn($statusCode);
        $responseMock->shouldReceive('object')
            ->once()->andReturn($mockedOject);
        $realWeightResponseProcessorMock = Mockery::mock(RealWeightResponseProcessor::class)->makePartial();

        $realWeightResponseProcessorMock->shouldReceive('setWeightRealSyncAt')
            ->once()->with($mockedOject)->andReturns();
        $returned = $realWeightResponseProcessorMock->processResponse($responseMock);
        $this->assertTrue($returned === null);
    }

    /**
     * @dataProvider responseSuccessData
     */
    public function testSetWeightRealSyncAtSuccessData($responseData)
    {
        $loggerMock = Mockery::mock(\App\Models\Logger\Logger::class);
        $realWeightResponseProcessor = new RealWeightResponseProcessor(
            new BoxWeightRepository(),
            $loggerMock
        );

        $loggerMock->shouldReceive('logError')->never();
        $loggerMock->shouldReceive('logInfo')->once();
        $realWeightResponseProcessor->setWeightRealSyncAt($responseData);
        $this->notSeeInDatabase('box_weight', ['weight_real_sync_at' => null]);
    }

    /**
     * @dataProvider responsePartialSuccessData
     */
    public function testSetWeightRealSyncAtPartialSuccessData($responseData)
    {
        $loggerMock = Mockery::mock(\App\Models\Logger\Logger::class);
        $realWeightResponseProcessor = new RealWeightResponseProcessor(
            new BoxWeightRepository(),
            $loggerMock
        );

        $loggerMock->shouldReceive('logError')->once();
        $loggerMock->shouldReceive('logInfo')->once();
        $realWeightResponseProcessor->setWeightRealSyncAt($responseData);
        $this->seeInDatabase('box_weight', ['weight_real_sync_at' => null]);
        //$this->seeInDatabase('box_weight', ['weight_real_sync_at' => !null]);
    }

    /**
     * @dataProvider responseErrorData
     */
    public function testSetWeightRealSyncAtErrorData($responseData)
    {
        $loggerMock = Mockery::mock(\App\Models\Logger\Logger::class);
        $realWeightResponseProcessor = new RealWeightResponseProcessor(
            new BoxWeightRepository(),
            $loggerMock
        );

        $loggerMock->shouldReceive('logError')->twice();
        $loggerMock->shouldReceive('logInfo')->once();
        $realWeightResponseProcessor->setWeightRealSyncAt($responseData);
        $this->seeInDatabase('box_weight', ['weight_real_sync_at' => null]);
    }

    public function responseSuccessData()
    {
        return [
            [
                (object) [
                    'data' => [
                        '{"message":"success","resource":{"order_id":10724512},"status":200}',
                        '{"message":"success","resource":{"order_id":10724515},"status":200}'
                    ]
                ]
            ]
        ];
    }

    public function responsePartialSuccessData()
    {
        return [
            [
                (object) [
                    'data' => [
                        '{"message":"success","resource":{"order_id":10724512},"status":200}',
                        '{"message":"success","resource":{"order_id":300},"status":200}'
                    ]
                ]
            ]
        ];
    }

    public function responseErrorData()
    {
        return [
            [
                (object) [
                    'data' => [
                        '{"message":"success","resource":{"order_id":300},"status":200}',
                        '{"message":"success","resource":{"order_id":400},"status":200}'
                    ]
                ]
            ]
        ];
    }
}
