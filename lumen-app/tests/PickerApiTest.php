<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Repositories\BoxWeightRepository;

class PickerApiTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testSetBoxUidNoData()
    {
        $this->json("PUT", 'api/V1/picker', [], $this->getHeader());
        $this->seeStatusCode(422);
        $this->seeJsonContains([
            "order_id" => ["validation.required"],
            "box_uid" => ["validation.required"],
            "picker_id" => ["validation.required"]
        ]);
    }

    public function testSetBoxUidCorrectData()
    {
        $data = $this->getData();
        $this->json("PUT", 'api/V1/picker', $data, $this->getHeader());
        $this->seeStatusCode(200);
        $this->seeJsonContains(["status" => "success"]);
        $this->seeInDatabase('box_weight', $data);
    }

    public function testSetBoxUidDbError()
    {
        $boxWeightRepoMock = Mockery::mock(BoxWeightRepository::class);
        $boxWeightRepoMock->shouldReceive('persistPickerData')->andReturn(\Exception::class);
        $this->app->instance(BoxWeightRepository::class, $boxWeightRepoMock);
        $this->json("PUT", 'api/V1/picker', $this->getData(), $this->getHeader());
        $this->seeStatusCode(500);
        $this->seeJsonContains(["status" => "error"]);
    }

    public function testSetBoxUidBadToken()
    {
        $data = $this->getData();
        $header = $this->getHeader();
        $header['Authorization'] = 'Bearer ' . env('APP_PICKER_AUTH_TOKEN') . 'p9s';
        $this->json("PUT", 'api/V1/picker', $data, $header);
        $this->seeStatusCode(401);
        $this->response->assertSeeText('Unauthorized');
        $this->notSeeInDatabase('box_weight', $data);
    }

    private function getData()
    {
        return [
            "order_id" => rand(1000000,9999999),
            "box_uid" => rand(10000,99999),
            "picker_id" => rand(100,999)
        ];
    }

    private function getHeader()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('APP_PICKER_AUTH_TOKEN')
        ];
    }
}
