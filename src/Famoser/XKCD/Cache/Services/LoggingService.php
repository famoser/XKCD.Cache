<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 14.11.2016
 * Time: 12:42
 */

namespace Famoser\SyncApi\Services;

use Famoser\SyncApi\Services\Base\BaseService;
use Famoser\SyncApi\Services\Interfaces\LoggingServiceInterface;

/**
 * the logger service is concerned to save errors which occurred while the application is running
 *
 * @package Famoser\SyncApi\Services
 */
class LoggingService extends BaseService implements LoggingServiceInterface
{
    /**
     * log your message
     *
     * @param $message
     */
    public function log($message)
    {
        $path = $this->getLoggingFilePath();
        file_put_contents($path, $message, FILE_APPEND);
    }

    /**
     * get path where the log files are saved
     *
     * @return string
     */
    public function getLogPath()
    {
        return $this->getLoggingFilePath();
    }
}