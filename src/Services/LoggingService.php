<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 14.11.2016
 * Time: 12:42
 */

namespace Famoser\XKCDCache\Services;

use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\LoggingServiceInterface;

/**
 * the logger service is concerned to save errors which occurred while the application is running
 *
 * @package Famoser\XKCDCache\Services
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
        $path = $this->getSettingService()->getLogFilePath();
        file_put_contents($path, $message, FILE_APPEND);
    }
}