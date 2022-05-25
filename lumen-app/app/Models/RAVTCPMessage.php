<?php

namespace App\Models;

class RAVTCPMessage extends TCPMessage
{
    protected string $messageType;
    protected int $dateTime;
    protected int $messageNumber;
    protected int $rfidBox;
    protected int $weightBox;

    public static function getExpectedMessageLength(): int
    {
        return 40;
    }

    public static function getExpectedMessageType(): string
    {
        return 'RAV';
    }

    public static function getExpectedDataFieldsCount(): int
    {
        return 5;
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
    public function getDateTime(): int
    {
        return $this->dateTime;
    }

    /**
     * @param int $dateTime
     */
    public function setDateTime(int $dateTime): void
    {
        $this->dateTime = $dateTime;
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
     * @return int
     */
    public function getRfidBox(): int
    {
        return $this->rfidBox;
    }

    /**
     * @param int $rfidBox
     */
    public function setRfidBox(int $rfidBox): void
    {
        $this->rfidBox = $rfidBox;
    }

    /**
     * @return int
     */
    public function getWeightBox(): int
    {
        return $this->weightBox;
    }

    /**
     * @param int $weightBox
     */
    public function setWeightBox(int $weightBox): void
    {
        $this->weightBox = $weightBox;
    }
}