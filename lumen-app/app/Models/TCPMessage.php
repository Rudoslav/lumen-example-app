<?php

namespace App\Models;

abstract class TCPMessage
{

    abstract public static function getExpectedMessageLength(): int;

    abstract public static function getExpectedDataFieldsCount(): int;

    abstract public static function getExpectedMessageType(): string;

    public static function getDataSeparator(): string
    {
        return ';';
    }
}