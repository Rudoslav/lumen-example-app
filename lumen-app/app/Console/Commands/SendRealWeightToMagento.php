<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */
declare(strict_types=1);

namespace App\Console\Commands;

use App\Api\RealWeightResponseProcessorInterface;
use App\Exceptions\Service\ApiErrorResponseException;
use App\Models\BoxWeight;
use App\Api\BoxWeightInterface;
use App\Api\RealWeightServiceInterface;
use App\Models\Logger\Logger;
use App\Exceptions\Service\ApiErrorConfigException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SendRealWeightToMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'box-weight:to:magento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the measured real weight of orders to Magento';

    protected BoxWeight $boxWeight;
    protected RealWeightServiceInterface $realWeightService;
    protected RealWeightResponseProcessorInterface $realWeightResponseProcessor;
    protected Logger $logger;

    /**
     * @param BoxWeight $boxWeight
     * @param RealWeightServiceInterface $realWeightService
     * @param RealWeightResponseProcessorInterface $realWeightResponseProcessor
     * @param Logger $logger
     */
    public function __construct(
        BoxWeight $boxWeight,
        RealWeightServiceInterface $realWeightService,
        RealWeightResponseProcessorInterface $realWeightResponseProcessor,
        Logger $logger
    ) {
        $this->boxWeight = $boxWeight;
        $this->realWeightService = $realWeightService;
        $this->realWeightResponseProcessor = $realWeightResponseProcessor;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $collection = $this->getBoxWeightCollection();
        if ($collection->count() == 0) {
            $this->logger->logInfo('There are 0 real weights to sync, skipping run...');
            return;
        }
        try {
            $response = $this->realWeightService->post($collection);
            $this->realWeightResponseProcessor->processResponse($response);
        } catch (ApiErrorConfigException $exception) {
            $this->logger->logCritical('API configuration error: ' . $exception->getMessage());
        } catch (ApiErrorResponseException $exception) {
	        $this->logger->logCritical(
                __CLASS__.': API response error: ' . $exception->getMessage().' failed data: '.$collection->toJson()
            );
        } catch (\Exception $exception) {
            $this->logger->logCritical('Unknown error: ' . $exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getBoxWeightCollection(): Collection
    {
        return $this->boxWeight
            ->getQuery()
            ->whereNotNull(BoxWeightInterface::WEIGHT_REAL)
            ->whereNull(BoxWeightInterface::WEIGHT_REAL_SYNC_AT)
            ->limit(BoxWeightInterface::MAX_SYC_ITEMS)
            ->get([
                BoxWeightInterface::ORDER_ID,
                BoxWeightInterface::BOX_UID,
                BoxWeightInterface::WEIGHT_REAL,
                BoxWeightInterface::PICKER_ID
            ]);
    }
}
