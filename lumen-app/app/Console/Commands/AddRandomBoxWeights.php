<?php

namespace App\Console\Commands;

use App\Models\BoxWeightFactory;
use App\Services\BoxWeightService;
use Illuminate\Console\Command;

class AddRandomBoxWeights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'box-weight:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds 10 random weights to box_weight table';

    protected BoxWeightFactory $boxWeightFactory;
    protected BoxWeightService $boxWeightService;

    /**
     * @param BoxWeightFactory $boxWeightFactory
     * @param BoxWeightService $boxWeightService
     */
    public function __construct(BoxWeightFactory $boxWeightFactory, BoxWeightService $boxWeightService)
    {
        parent::__construct();
        $this->boxWeightFactory = $boxWeightFactory;
        $this->boxWeightService = $boxWeightService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \App\Exceptions\Model\CouldNotSaveException
     */
    public function handle()
    {
        for ($i = 0; $i <= 10; $i++) {
            $boxWeight = $this->boxWeightFactory->create(
                random_int(10, 1000),
                random_int(10, 1000),
                random_int(10, 1000),
                random_int(10, 10000),
                random_int(10, 10000),
                random_int(20110121073016, 20310121073016),
                random_int(10, 1000),
                'g',
                'g'
            );
            $this->boxWeightService->save($boxWeight);
        }
    }
}
