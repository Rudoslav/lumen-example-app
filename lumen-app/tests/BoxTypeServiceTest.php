<?php

class BoxTypeServiceTest extends TestCase
{
    protected $boxTypeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boxTypeService = new \App\Services\BoxTypeService();
    }

    public function testGetBoxTypeOutputData()
    {
        $this->assertSame(
            1111,
            $this->boxTypeService->getBoxType(11111365)
        );
        $this->assertSame(
            2222,
            $this->boxTypeService->getBoxType(22221365)
        );
        $this->assertSame(
            11,
            $this->boxTypeService->getBoxType(11)
        );
        $this->assertSame(
            0,
            $this->boxTypeService->getBoxType(0)
        );
    }
}