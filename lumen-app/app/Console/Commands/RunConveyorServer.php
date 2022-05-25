<?php

namespace App\Console\Commands;

use App\Models\ACKTCPMessage;
use App\Services\BoxWeightService;
use App\Services\ConveyorBeltService;
use App\Services\LogService;
use App\Api\WeightToleranceInterface;
use Exception;
use Illuminate\Console\Command;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class RunConveyorServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conveyor-tcp:run {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run TCP server for conveyor belt on specified port';
    protected ConveyorBeltService $conveyorBeltService;
    protected BoxWeightService $boxWeightService;
    protected LogService $logService;
    protected WeightToleranceInterface $weightToleranceService;

    /**
     * Create a new command instance.
     *
     * @param ConveyorBeltService $conveyorBeltService
     * @param BoxWeightService $boxWeightService
     * @param LogService $logService
     * @param WeightToleranceInterface $weightToleranceService
     */
    public function __construct(
        ConveyorBeltService $conveyorBeltService,
        BoxWeightService $boxWeightService,
        LogService $logService,
        WeightToleranceInterface $weightToleranceService
    ) {
        parent::__construct();
        $this->conveyorBeltService = $conveyorBeltService;
        $this->boxWeightService = $boxWeightService;
        $this->logService = $logService;
        $this->weightToleranceService = $weightToleranceService;
    }

    public function handle()
    {
        $this->logService->log(__CLASS__, LogService::LEVEL_INFO, "Starting TCP server...");
        $socket = new SocketServer('0.0.0.0:'.$this->argument('port') ?: '10000');
        $socket->on('connection', function (ConnectionInterface $connection) {
            $this->logService->log(__CLASS__, LogService::LEVEL_INFO, "Incoming connection from: ".$connection->getRemoteAddress());
            $connection->on('data', function ($data) use ($connection) {
                $this->onData($connection, $data);
            });
        });
        $socket->on('error', function (Exception $exception) {
            $this->onError($exception);
        });
    }

    /**
     * @param ConnectionInterface $connection
     * @param $data
     */
    protected function onData(ConnectionInterface $connection, $data)
    {
        $addressFrom = $connection->getRemoteAddress();
        $this->logService->log(__CLASS__, LogService::LEVEL_INFO,"Incoming data from: $addressFrom", ['data' => $data]);
        $ackTcpResponse = $this->conveyorBeltService->createACKTCPMessage(0, 'OKA', 0);
        try {
            $ravTcpMessage = $this->conveyorBeltService->parseRAVTCPMessage($data);
            $ackTcpResponse->setMessageNumber($ravTcpMessage->getMessageNumber());
            $boxWeight = $this->boxWeightService->findByBoxUID($ravTcpMessage->getRfidBox());
            if (!$boxWeight) {
                $ackTcpResponse->setMessageStatus('ERR');
                $ackTcpResponse->setMessageStatusNum(1);
                $this->logService->log(
                    __CLASS__,
                    LogService::LEVEL_WARNING,
                    "BoxWeight for box UID: ".$ravTcpMessage->getRfidBox()." not found"
                );
                $this->respond($connection, $ackTcpResponse);
                return;
            }
            if ($boxWeight->getWeightReal()) {
                $this->logService->log(
                    __CLASS__,
                    LogService::LEVEL_WARNING,
                    sprintf(
                        "Weight ID %s was changed from %s to %s for order %s",
                        $boxWeight->getId(),
                        $boxWeight->getWeightReal(),
                        $ravTcpMessage->getWeightBox(),
                        $boxWeight->getOrderId()
                    )
                );
            }
            $boxWeight->setWeightReal($ravTcpMessage->getWeightBox());
            $boxWeight->setWeightRealSyncAt(null);
            $this->boxWeightService->save($boxWeight);

            if (!$this->weightToleranceService->isWeightAccepted(
                    $boxWeight->getWeightExpected(),
                    $ravTcpMessage->getWeightBox(),
                    $ravTcpMessage->getRfidBox()
            )) {
                $this->logService->log(
                    __CLASS__,
                    LogService::LEVEL_WARNING,
                    "Weight mismatch for box UID ".$ravTcpMessage->getRfidBox(),
                    [
                        'exp' => $boxWeight->getWeightExpected(),
                        'real' => $ravTcpMessage->getWeightBox(),
                        'box' => $ravTcpMessage->getRfidBox(),
                        'order' => $boxWeight->getOrderId()
                    ]
                );
                $ackTcpResponse->setMessageStatus('ERR');
                $ackTcpResponse->setMessageStatusNum(2);
                $this->respond($connection, $ackTcpResponse);
                return;
            }
            $this->respond($connection, $ackTcpResponse);
        } catch (Exception $exception) {
            $ackTcpResponse->setMessageStatus('ERR');
            $ackTcpResponse->setMessageStatusNum(3);
            $this->logService->log(
                __CLASS__,
                LogService::LEVEL_WARNING,
                "Error in communication: ".$exception->getMessage(),
                [
                    'from' => $addressFrom,
                    'data' => $data,
                    'ack' => $ackTcpResponse
                ]
            );
            $this->respond($connection, $ackTcpResponse);
        }
    }

    /**
     * @param Exception $exception
     */
    protected function onError(Exception $exception)
    {
        $this->logService->log(
            __CLASS__,
            LogService::LEVEL_ERROR,
            "An error occurred when connecting client to TCP server: ".$exception->getMessage()
        );
    }

    /**
     * @param ConnectionInterface $connection
     * @param ACKTCPMessage $ACKTCPMessage
     */
    protected function respond(ConnectionInterface $connection, ACKTCPMessage $ACKTCPMessage)
    {
        $this->logService->log(
            __CLASS__,
            LogService::LEVEL_INFO,
            "Responding to ".$connection->getRemoteAddress()." with: $ACKTCPMessage\n"
            ."============================================================================"
        );
        $connection->end($ACKTCPMessage->__toString());
    }
}