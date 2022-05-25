<?php

use App\Api\BoxWeightInterface;
use App\Services\BoxWeightService;
use App\Repositories\BoxWeightRepository;
use App\Models\BoxWeightFactory;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BoxWeightServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected BoxWeightService $boxWeightService;
    protected BoxWeightRepository $boxWeightRepository;
    protected BoxWeightFactory $boxWeightFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boxWeightRepository = new BoxWeightRepository();
        $this->boxWeightService = new BoxWeightService($this->boxWeightRepository);
        $this->boxWeightFactory = new BoxWeightFactory();
    }

    public function testSaveNewSuccess()
    {
        $boxWeight = $this->boxWeightFactory->create(
            100,
            0,
            123456,
            123,
            1234567,
            123456789123,
            333,
            'g',
            'g'
        );
        $this->boxWeightService->save($boxWeight);
        $this->seeInDatabase(BoxWeightInterface::TABLE, [BoxWeightInterface::ID => $boxWeight->getId()]);
    }

    public function testFindByBoxUIDSuccess()
    {
        $boxWeight = $this->boxWeightFactory->create(
            100,
            0,
            123456,
            123,
            99999999,
            123456789123,
            333,
            'g',
            'g'
        );
        $this->boxWeightService->save($boxWeight);
        $this->assertTrue(($this->boxWeightService->findByBoxUID(99999999) instanceof BoxWeightInterface));
    }

    public function testFindByOrderIdSuccess()
    {
        $boxWeight = $this->boxWeightFactory->create(
            100,
            0,
            7777777,
            123,
            99999999,
            123456789123,
            333,
            'g',
            'g'
        );
        $this->boxWeightService->save($boxWeight);
        $this->assertTrue(($this->boxWeightService->findByOrderID(7777777) instanceof BoxWeightInterface));
    }
}
