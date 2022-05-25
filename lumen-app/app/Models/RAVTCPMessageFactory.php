<?php

namespace App\Models;

class RAVTCPMessageFactory
{
    public function create(string $messageType, int $dateTime, int $messageNumber, int $rfidBox, int $weightBox): RAVTCPMessage
    {
        $message = new RAVTCPMessage();
        $message->setMessageType($messageType);
        $message->setDateTime($dateTime);
        $message->setMessageNumber($messageNumber);
        $message->setRfidBox($rfidBox);
        $message->setWeightBox($weightBox);
        return $message;
    }
}