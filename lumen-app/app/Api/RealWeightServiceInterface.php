<?php

namespace App\Api;

use Illuminate\Support\Collection;
use Illuminate\Http\Client\Response;

interface RealWeightServiceInterface
{
    /**
     * @param Collection $boxWeightCollection
     * @return string
     */
    public function post(Collection $boxWeightCollection): Response;
}