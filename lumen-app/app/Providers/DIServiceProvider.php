<?php

namespace App\Providers;

use App\Api\BoxTypeServiceInterface;
use App\Api\BoxWeightInterface;
use App\Api\BoxWeightRepositoryInterface;
use App\Api\BoxWeightToleranceServiceInterface;
use App\Api\RealWeightResponseProcessorInterface;
use App\Api\RealWeightServiceInterface;
use App\Api\WeightToleranceInterface;
use App\Services\BoxTypeService;
use App\Services\BoxWeightToleranceService;
use App\Services\WeightToleranceService;
use App\Models\BoxWeight;
use App\Repositories\BoxWeightRepository;
use Illuminate\Support\ServiceProvider;
use App\Api\ExpectedWeightInterface;
use App\Api\ExpectedWeightServiceInterface;
use App\Models\RealWeightResponseProcessor;
use App\Models\ExpectedWeight;
use App\Services\RealWeightService;
use App\Services\ExpectedWeightService;

class DIServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BoxWeightInterface::class, BoxWeight::class);
        $this->app->bind(BoxWeightRepositoryInterface::class, BoxWeightRepository::class);

        $this->app->bind(WeightToleranceInterface::class, WeightToleranceService::class);
        $this->app->bind(BoxWeightToleranceServiceInterface::class, BoxWeightToleranceService::class);

        $this->app->bind(ExpectedWeightInterface::class, ExpectedWeight::class);
        $this->app->bind(ExpectedWeightServiceInterface::class, ExpectedWeightService::class);

        $this->app->bind(RealWeightResponseProcessorInterface::class, RealWeightResponseProcessor::class);
        $this->app->bind(RealWeightServiceInterface::class, RealWeightService::class);

        $this->app->bind(BoxTypeServiceInterface::class, BoxTypeService::class);
    }
}