<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 14.11.2016
 * Time: 12:42
 */

namespace Famoser\XKCDCache\Services\Interfaces;


/**
 * the interface to a logger, which logs errors
 * @package Famoser\XKCDCache\Services\Interfaces
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
}