<?php

namespace App\Services;

class BoxTypeService implements \App\Api\BoxTypeServiceInterface
{
    public function getBoxType(int $boxUID): int
    {
        return (int)substr((string)$boxUID, 0, 4);
    }
}