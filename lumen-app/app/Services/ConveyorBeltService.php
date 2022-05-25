<?php

namespace App\Services;

use App\Api\BoxTypeServiceInterface;
use App\Exceptions\Service\InvalidTCPMessageDataException;
use App\Exceptions\Service\InvalidTCPMessageLengthException;
use App\Exceptions\Service\InvalidTCPMessageTypeException;
use App\Models\ACKTCPMessage;
use App\Models\ACKTCPMessageFactory;
use App\Models\RAVTCPMessage;
use App\Models\RAVTCPMessageFactory;
use App\Models\TCPMessage;

class ConveyorBeltService
{
    protected RAVTCPMessageFactory $RAVTCPMessageFactory;
    protected ACKTCPMessageFactory $ACKTCPMessageFactory;
    protected BoxTypeServiceInterface $boxTypeService;
    private array $boxWeightCalibration;

    /**
     * @param RAVTCPMessageFactory $RAVTCPMessageFactory
     * @param ACKTCPMessageFactory $ACKTCPMessageFactory
     * @param BoxTypeServiceInterface $boxTypeService
     */
    public function __construct(
        RAVTCPMessageFactory $RAVTCPMessageFactory,
        ACKTCPMessageFactory $ACKTCPMessageFactory,
        BoxTypeServiceInterface $boxTypeService
    ) {
        $this->RAVTCPMessageFactory = $RAVTCPMessageFactory;
        $this->ACKTCPMessageFactory = $ACKTCPMessageFactory;
        $this->boxTypeService = $boxTypeService;
    }

    /**
     * @param $message
     * @return RAVTCPMessage
     * @throws InvalidTCPMessageDataException
     * @throws InvalidTCPMessageLengthException
     * @throws InvalidTCPMessageTypeException
     */
    public function parseRAVTCPMessage($message): RAVTCPMessage
    {
	    $message = trim((string)$message);
        $explodedMessage = $this->validateTCPMessage(new RAVTCPMessage(), $message);
        $boxUID = (int)$explodedMessage[3];
        if ($boxUID === 0) {
            throw new InvalidTCPMessageDataException('Message box uid "'.$explodedMessage[3].'" is invalid ');
        }
        $rawWeight = (int)str_replace('g', '', $explodedMessage[4]);
        return $this->RAVTCPMessageFactory->create(
            $explodedMessage[0],
            $explodedMessage[1],
            $explodedMessage[2],
            $boxUID,
            $this->getAdjustedWeight($boxUID, $rawWeight)
        );
    }

    /**
     * @param int $boxUID
     * @param int $rawWeight
     * @return int
     * @throws InvalidTCPMessageDataException
     */
    public function getAdjustedWeight(int $boxUID, int $rawWeight): int
    {
        $boxType = $this->getBoxType($boxUID);
        $boxWeightCalibration = $this->getBoxWeightCalibration();

        if (!array_key_exists($boxType, $boxWeightCalibration)) {
            throw new \Exception('Unrecognized box type: ' . $boxType);
        }

        $realWeight = $rawWeight - $boxWeightCalibration[$boxType];
        if ($realWeight < 0) {
            throw new InvalidTCPMessageDataException('Unadjustable box weight. Weight is low as empty box for Box UID: ' . $boxUID);
        }
        return $realWeight;
    }

    /**
     * @param int $boxUID
     * @return int
     */
    protected function getBoxType(int $boxUID): int
    {
        return $this->boxTypeService->getBoxType($boxUID);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getBoxWeightCalibration(): array
    {
        if (!isset($this->boxWeightCalibration)) {
            $this->boxWeightCalibration = json_decode(env('BOX_WEIGHT_CALIBRATION'), true);
            foreach ($this->boxWeightCalibration as $value) {
                if (!preg_match('/^\d+$/', $value)) {
                    throw new \Exception('Box weight calibration value must be a integer. Actual value: ' . $value);
                }
            }
        }
        return $this->boxWeightCalibration;
    }

    /**
     * @param int $messageNumber
     * @param string $messageStatus
     * @param int $messageStatusNum
     * @return ACKTCPMessage
     */
    public function createACKTCPMessage(
        int $messageNumber,
        string $messageStatus,
        int $messageStatusNum
    ): ACKTCPMessage
    {
        return $this->ACKTCPMessageFactory->create(
            $messageNumber,
            $messageStatus,
            $messageStatusNum
        );
    }

    /**
     * @param TCPMessage $type
     * @param string $rawMessage
     * @return array - exploded message by separator
     * @throws InvalidTCPMessageDataException
     * @throws InvalidTCPMessageLengthException
     * @throws InvalidTCPMessageTypeException
     */
    public function validateTCPMessage(TCPMessage $type, string $rawMessage): array
    {
        $messageType = substr($rawMessage, 0, 3);
        if ($messageType !== $type::getExpectedMessageType()) {
            throw new InvalidTCPMessageTypeException(
                "Message type should be ".$type::getExpectedMessageType().", but received $messageType"
            );
        }
        $explodedMessage = explode($type::getDataSeparator(), $rawMessage);
        if (!$explodedMessage) {
            throw new InvalidTCPMessageDataException("Could not explode RAV TCP message");
        }
        if (count($explodedMessage) !== $type::getExpectedDataFieldsCount()) {
            throw new InvalidTCPMessageDataException(
                "Message should contain ".$type::getExpectedDataFieldsCount()
                ." fields, but received ".count($explodedMessage)
            );
        }
        for($i = 0; $i < $type::getExpectedDataFieldsCount(); $i++) {
            if (!array_key_exists($i, $explodedMessage)) {
                throw new InvalidTCPMessageDataException(
                    "Missing $i key of message, received message: ".print_r($explodedMessage, true)
                );
            }
        }
        return $explodedMessage;
    }
}