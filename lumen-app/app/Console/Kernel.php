<?php

namespace App\Console;

use App\Console\Commands\AddRandomBoxWeights;
use App\Console\Commands\RunConveyorServer;
use App\Console\Commands\SendRealWeightToMagento;
use App\Console\Commands\SyncExpectedWeights;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RunConveyorServer::class,
        AddRandomBoxWeights::class,
        SendRealWeightToMagento::class,
        SyncExpectedWeights::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(SyncExpectedWeights::class, [env('APP_BOX_WEIGHT_SYNC_NUM')])
            ->everyThreeMinutes();
        $schedule->command(SendRealWeightToMagento::class)
            ->everyThreeMinutes();
    }
}
