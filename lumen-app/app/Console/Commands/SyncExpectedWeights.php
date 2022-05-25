<?php

namespace App\Console\Commands;

use App\Api\ExpectedWeightInterface;
use App\Models\BoxWeightFactory;
use App\Api\ExpectedWeightServiceInterface;
use App\Models\ExpectedWeightFactory;
use App\Services\BoxWeightService;
use App\Services\LogService;
use Illuminate\Console\Command;

class SyncExpectedWeights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'box-weight:sync {num}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asks for {num} of expected weights from remote service and saves them in the DB.';

    protected BoxWeightFactory $boxWeightFactory;
    protected BoxWeightService $boxWeightService;
    protected ExpectedWeightFactory $expectedWeightFactory;
    protected ExpectedWeightServiceInterface $expectedWeightService;
    protected LogService $logService;

    /**
     * @param BoxWeightFactory $boxWeightFactory
     * @param BoxWeightService $boxWeightService
     * @param ExpectedWeightFactory $expectedWeightFactory
     * @param ExpectedWeightServiceInterface $expectedWeightService
     * @param LogService $logService
     */
    public function __construct(
        BoxWeightFactory $boxWeightFactory,
        BoxWeightService $boxWeightService,
        ExpectedWeightFactory $expectedWeightFactory,
        ExpectedWeightServiceInterface $expectedWeightService,
        LogService $logService
    ) {
        parent::__construct();
        $this->boxWeightFactory = $boxWeightFactory;
        $this->boxWeightService = $boxWeightService;
        $this->expectedWeightFactory = $expectedWeightFactory;
        $this->expectedWeightService = $expectedWeightService;
        $this->logService = $logService;
    }

    public function handle()
    {
        $this->logInfo("starting sync of expected weights...");
        $maxNumOfWeights = (int)$this->argument('num');
        if (!$maxNumOfWeights) {
            throw new \RuntimeException("The num argument must be an int > 0");
        }
        $expectedWeights = [];
        try {
            $expectedWeights = $this->expectedWeightService->get($maxNumOfWeights);
        } catch (\Exception $exception) {
            $this->logService->log(
                __CLASS__,
                LogService::LEVEL_CRITICAL,
                'fetching expected weights failed; message: '.$exception->getMessage()
            );
        }
        if (count($expectedWeights) === 0) {
            $this->logInfo('there are 0 expected weights to sync, skipping run...');
            return;
        }
        $this->logInfo('processing '.count($expectedWeights).' weights...');
        foreach ($expectedWeights as $expectedWeight) {
            if ($expectedWeight->getWeightExpected() === 0) {
                $this->logService->log(
                    __CLASS__,
                    LogService::LEVEL_WARNING,
                    'received expected weight for order id '.$expectedWeight->getOrderId()
                    .' is 0, data will be saved, but please check data on remote server'
                );
            }
            $boxWeight = $this->boxWeightService->findByOrderID($expectedWeight->getOrderId());
            if ($boxWeight === null) {
                $boxWeight = $this->createBoxWeight($expectedWeight);
            } elseif ($expectedWeight->getWeightExpected() === $boxWeight->getWeightExpected()) {
                continue;
            } else {
                $this->logInfo(
                    "changing weight_expected from ".$boxWeight->getWeightExpected()." to "
                    .$expectedWeight->getWeightExpected()." for order ".$boxWeight->getOrderId()."in box "
                    .$boxWeight->getBoxUID()
                );
                $boxWeight->setWeightExpected($expectedWeight->getWeightExpected());
            }
            try {
                $this->boxWeightService->save($boxWeight);
            } catch (\Exception $exception) {
                $this->logService->log(
                    __CLASS__,
                    LogService::LEVEL_ERROR,
                    'error saving weight: '.$boxWeight->getWeightExpected().', for order ID: '.$boxWeight->getOrderId()
                    .'; message: '.$exception->getMessage()
                );
            }
        }
        $this->logInfo("ended sync of expected weights.");
    }

    /**
     * @param ExpectedWeightInterface $expectedWeight
     * @return \App\Api\BoxWeightInterface
     */
    protected function createBoxWeight(ExpectedWeightInterface $expectedWeight): \App\Api\BoxWeightInterface
    {
        return $this->boxWeightFactory->create(
            $expectedWeight->getWeightExpected(),
            null,
            $expectedWeight->getOrderId(),
            0,
            0,
            0,
            0,
            'g',
            'g'
        );
    }

    protected function logInfo(string $message)
    {
        $this->logService->log(__CLASS__, LogService::LEVEL_INFO, $message);
    }

}