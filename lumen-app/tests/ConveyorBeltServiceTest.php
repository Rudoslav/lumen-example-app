<?php

use App\Exceptions\Service\InvalidTCPMessageDataException;
use App\Models\ACKTCPMessage;
use App\Models\ACKTCPMessageFactory;
use App\Models\RAVTCPMessage;
use App\Models\RAVTCPMessageFactory;
use App\Services\ConveyorBeltService;

class ConveyorBeltServiceTest extends TestCase
{
    protected ConveyorBeltService $conveyorBeltService;
    protected ACKTCPMessageFactory $ACKTCPMessageFactory;
    protected RAVTCPMessageFactory $RAVTCPMessageFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ACKTCPMessageFactory = new ACKTCPMessageFactory();
        $this->RAVTCPMessageFactory = new RAVTCPMessageFactory();
        $this->conveyorBeltService = new ConveyorBeltService(
            $this->RAVTCPMessageFactory,
            $this->ACKTCPMessageFactory,
            new \App\Services\BoxTypeService()
        );
    }

    public function testValidateTCPMessageRAVDelimiterException()
    {
        $input = 'RAV,20210121073016,000000001,BOX000007691,00312g';
        $this->expectException(\Exception::class);
        $this->conveyorBeltService->validateTCPMessage(new RAVTCPMessage(), $input);
    }

    public function testValidateTCPMessageRAVLengthException()
    {
        $input = '51654;51654';
        $this->expectException(\Exception::class);
        $this->conveyorBeltService->validateTCPMessage(new RAVTCPMessage(), $input);
    }

    public function testValidateTCPMessageRAVFieldsException()
    {
        $input = 'RAV;20210121073016;0000;00001;BOX0000;691;00312g';
        $this->expectException(\Exception::class);
        $this->conveyorBeltService->validateTCPMessage(new RAVTCPMessage(), $input);
    }

    public function testValidateTCPMessageRAVMessageTypeException()
    {
        $input = 'ACK;20210121073016;000000001;BOX000007691;00312g';
        $this->expectException(\Exception::class);
        $this->conveyorBeltService->validateTCPMessage(new RAVTCPMessage(), $input);
    }

    public function testValidateTCPMessageRAVCorrect()
    {
        $input = 'RAV;20211210141645;374584;11111365;1840g';
        $this->assertTrue(
            count($this->conveyorBeltService->validateTCPMessage(new RAVTCPMessage(), $input))
            === RAVTCPMessage::getExpectedDataFieldsCount()
        );
    }

    //

    public function testParseRAVTCPMessageReturnType()
    {
        $input = 'RAV;20211210141645;374584;11111365;1840g';
        $this->assertTrue(($this->conveyorBeltService->parseRAVTCPMessage($input) instanceof RAVTCPMessage));
    }

    public function testCreateACKTCPMessageReturnType()
    {
        $this->assertTrue(
            ($this->conveyorBeltService->createACKTCPMessage(1, 'OKA', 0) instanceof ACKTCPMessage)
        );
    }

    public function testGetAdjustedWeight()
    {
        $boxUID = '11111365';
        $rawWeight = 1840;

        $conveyorBeltServiceMock = Mockery::mock(ConveyorBeltService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $conveyorBeltServiceMock->shouldReceive('getBoxWeightCalibration')->once()->andReturn([
            '1111' => 1610,
            '2222' => 1840
        ]);
        $conveyorBeltServiceMock->shouldReceive('getBoxType')
            ->once()
            ->andReturn(1111);

        $this->assertTrue($conveyorBeltServiceMock->getAdjustedWeight($boxUID, $rawWeight) > 0);
    }

    public function testGetAdjustedWeightBadUID()
    {
        $boxUID = '31111365';
        $rawWeight = 1840;
        $conveyorBeltServiceMock = Mockery::mock(ConveyorBeltService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $conveyorBeltServiceMock->shouldReceive('getBoxWeightCalibration')->once()->andReturn([
            '1111' => 1610,
            '2222' => 1840
        ]);
        $conveyorBeltServiceMock->shouldReceive('getBoxType')
            ->once()
            ->andReturn(3111);

        $this->expectException(Exception::class);
        $conveyorBeltServiceMock->getAdjustedWeight($boxUID, $rawWeight);
    }

    public function testGetAdjustedWeightBadWeight()
    {
        $boxUID = '2222365';
        $rawWeight = 840;
        $this->expectException(InvalidTCPMessageDataException::class);
        $this->conveyorBeltService->getAdjustedWeight($boxUID, $rawWeight);
    }

    public function testGetBoxWeightCalibration()
    {
        $this->assertTrue(gettype($this->conveyorBeltService->getBoxWeightCalibration()) == "array");
    }
}
