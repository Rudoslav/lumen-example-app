<?php

namespace App\Api;

interface BoxTypeServiceInterface
{
    public function getBoxType(int $boxUID): int;
}