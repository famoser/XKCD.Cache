<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 14.11.2016
 * Time: 12:42
 */

namespace Famoser\SyncApi\Services\Interfaces;


/**
 * the interface to a logger, which logs errors
 * @package Famoser\SyncApi\Services\Interfaces
 */
interface LoggingServiceInterface
{
    /**
     * log your message
     *
     * @param string $message
     * @return void
     */
    public function log($message);

    /**
     * get path where the log files are saved
     *
     * @return string
     */
    public function getLogPath();
}