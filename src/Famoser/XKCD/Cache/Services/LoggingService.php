<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 14.11.2016
 * Time: 12:42
 */

namespace Famoser\XKCD\Cache\Services;

use Famoser\XKCD\Cache\Services\Base\BaseService;
use Famoser\XKCD\Cache\Services\Interfaces\LoggingServiceInterface;

/**
 * the logger service is concerned to save errors which occurred while the application is running
 *
 * @package Famoser\XKCD\Cache\Services
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