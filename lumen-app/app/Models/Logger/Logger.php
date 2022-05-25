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

namespace App\Models\Logger;

use App\Services\LogService;

class Logger extends LogService
{
    /**
     * @param string $message
     */
    public function logInfo(string $message): void
    {
        $this->log(__CLASS__, LogService::LEVEL_INFO, $message);
    }

    /**
     * @param string $message
     */
    public function logWarning(string $message): void
    {
        $this->log(__CLASS__, LogService::LEVEL_WARNING, $message);
    }

    /**
     * @param string $message
     */
    public function logError(string $message): void
    {
        $this->log(__CLASS__, LogService::LEVEL_ERROR, $message);
    }

    /**
     * @param string $message
     */
    public function logCritical(string $message): void
    {
        $this->log(__CLASS__, LogService::LEVEL_CRITICAL, $message);
    }
}
