<?php

namespace App\Api;

use Illuminate\Http\Client\Response;

interface RealWeightResponseProcessorInterface
{
    public function processResponse(Response $response): void;
}