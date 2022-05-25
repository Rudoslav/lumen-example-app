<?php

namespace App\Models;

class ACKTCPMessageFactory
{
    public function create(int $messageNumber, string $messageStatus, int $messageStatusNum): ACKTCPMessage
    {
        $message = new ACKTCPMessage();
        $message->setMessageType(ACKTCPMessage::getExpectedMessageType());
        $message->setMessageNumber($messageNumber);
        $message->setMessageStatus($messageStatus);
        $message->setMessageStatusNum($messageStatusNum);
        return $message;
    }
}