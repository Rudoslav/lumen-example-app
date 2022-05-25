<?php

namespace App\Models;

class ACKTCPMessage extends TCPMessage
{
    protected string $messageType;
    protected int $messageNumber;
    protected string $messageStatus;
    protected int $messageStatusNum;

    public static function getExpectedMessageLength(): int
    {
        return 22;
    }

    public static function getExpectedMessageType(): string
    {
        return 'ACK';
    }

    public static function getExpectedDataFieldsCount(): int
    {
        return 4;
    }

    public function __toString(): string
    {
        return self::getExpectedMessageType().";"
		    .$this->getMessageNumber().";"
            .$this->getMessageStatus().";"
            .sprintf('%04d', $this->getMessageStatusNum())."\n";
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     */
    public function setMessageType(string $messageType): void
    {
        $this->messageType = $messageType;
    }

    /**
     * @return int
     */
    public function getMessageNumber(): int
    {
        return $this->messageNumber;
    }

    /**
     * @param int $messageNumber
     */
    public function setMessageNumber(int $messageNumber): void
    {
        $this->messageNumber = $messageNumber;
    }

    /**
     * @return string
     */
    public function getMessageStatus(): string
    {
        return $this->messageStatus;
    }

    /**
     * @param string $messageStatus
     */
    public function setMessageStatus(string $messageStatus): void
    {
        $this->messageStatus = $messageStatus;
    }

    /**
     * @return int
     */
    public function getMessageStatusNum(): int
    {
        return $this->messageStatusNum;
    }

    /**
     * @param int $messageStatusNum
     */
    public function setMessageStatusNum(int $messageStatusNum): void
    {
        $this->messageStatusNum = $messageStatusNum;
    }
}