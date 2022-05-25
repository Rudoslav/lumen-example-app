<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    public const LEVEL_INFO = 0;
    public const LEVEL_WARNING = 1;
    public const LEVEL_ERROR = 2;
    public const LEVEL_CRITICAL = 3;

    /**
     * @param string $fromClass
     * @param int $level
     * @param string $message
     * @param array $context
     */
    public function log(string $fromClass, int $level, string $message, array $context = [])
    {
        switch ($level) {
            case self::LEVEL_INFO:
                Log::info($fromClass.": $message\n", [$context]);
                break;
            case self::LEVEL_WARNING:
                Log::warning($fromClass.": $message\n", [$context]);
                break;
            case self::LEVEL_ERROR:
                Log::error($fromClass.": $message\n", [$context]);
                break;
            case self::LEVEL_CRITICAL:
                Log::critical($fromClass.": $message\n", [$context]);
                break;
            default:
                Log::debug($fromClass.": $message\n", [$context]);
                break;
        }
    }
}