<?php

use App\Services\ExpectedWeightService;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ExpectedWeightServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected $logService;
    protected $expectedWeightService;
    protected $expectedWeightFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logService = new \App\Services\LogService();
        $this->expectedWeightFactory = new \App\Models\ExpectedWeightFactory();
        $this->expectedWeightService = new ExpectedWeightService($this->logService, $this->expectedWeightFactory);
    }

    public function testCheckResponseSuccess()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $response->method('status')->willReturn(200);
        $this->assertTrue($this->expectedWeightService->checkResponse($response) === null);
    }

    public function testCheckResponseServerError()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $response->method('status')->willReturn(500);
        $this->expectExceptionObject(new \App\Exceptions\Service\ApiErrorResponseException('HTTP status code: 500'));
        $this->expectedWeightService->checkResponse($response);
    }

    public function testDecodeResponseErrorEmpty()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $response->method('body')->willReturn('');
        $this->expectExceptionObject(new \App\Exceptions\Service\ApiErrorResponseException('Response body is empty or could not be decoded'));
        $this->expectedWeightService->decodeResponse($response);
    }

    public function testDecodeResponseErrorCrap()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $response->method('body')->willReturn('123456874951748');
        $this->expectExceptionObject(new \App\Exceptions\Service\ApiErrorResponseException('Response body is empty or could not be decoded'));
        $this->expectedWeightService->decodeResponse($response);
    }

    public function testDecodeResponseSuccess()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $mockResponse = '[{"id":4,"order_id":5575582,"weight_expected":1274,"weight_real":null,"box_uid":"",'
            .'"needs_reindex":false,"picker_id":0,"additional_data":"","created_at":"2021-11-26 12:20:50","updated_at"'
            .':"2021-11-26 12:20:50"},{"id":5,"order_id":5575581,"weight_expected":778,"weight_real":null,"box_uid":""'
            .',"needs_reindex":false,"picker_id":0,"additional_data":"","created_at":"2021-11-26 12:20:59",'
            .'"updated_at":"2021-11-26 12:20:59"}]';
        $response->method('body')->willReturn($mockResponse);
        $this->assertTrue(count($this->expectedWeightService->decodeResponse($response)) === 2);
    }

    public function testDecodeResponseSomeWeightsMissingField()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $mockResponse = '[{"id":4,"order_id":5575582,"weight_expected":1274,"weight_real":null,"box_uid":"",'
            .'"needs_reindex":false,"picker_id":0,"additional_data":"","created_at":"2021-11-26 12:20:50","updated_at"'
            .':"2021-11-26 12:20:50"},{"id":5,"weight_expected":778,"weight_real":null,"box_uid":""'
            .',"needs_reindex":false,"picker_id":0,"additional_data":"","created_at":"2021-11-26 12:20:59",'
            .'"updated_at":"2021-11-26 12:20:59"}]';
        $response->method('body')->willReturn($mockResponse);
        $this->assertTrue(count($this->expectedWeightService->decodeResponse($response)) === 1);
    }

    public function testDecodeResponseSomeWeightsInvalidType()
    {
        $response = $this->createMock(\Illuminate\Http\Client\Response::class);
        $mockResponse = '[{"id":4,"order_id":5575582,"weight_expected":1274,"weight_real":null,"box_uid":"",'
            .'"needs_reindex":false,"picker_id":0,"additional_data":"","created_at":"2021-11-26 12:20:50","updated_at"'
            .':"2021-11-26 12:20:50"}, false]';
        $response->method('body')->willReturn($mockResponse);
        $this->assertTrue(count($this->expectedWeightService->decodeResponse($response)) === 1);
    }
}
