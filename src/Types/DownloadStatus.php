<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 27.05.2016
 * Time: 13:23
 */

namespace Famoser\XKCDCache\Types;

/**
 * any error which occurs by processing an api request
 *
 * @package Famoser\XKCDCache\Types
 */
class DownloadStatus
{
    const SUCCESSFUL = 0;
    const JSON_DOWNLOAD_FAILED = 1;
    const IMAGE_DOWNLOAD_FAILED = 2;

    const UNKNOWN_ERROR = 1000;

    /**
     * convert the api to a string
     *
     * @param $apiError
     * @return string
     */
    public static function toString($apiError)
    {
        switch ($apiError) {
            case self::SUCCESSFUL:
                return 'no error occurred';
            case self::JSON_DOWNLOAD_FAILED:
                return 'json download failed';
            case self::IMAGE_DOWNLOAD_FAILED:
                return 'image download failed';
            case self::UNKNOWN_ERROR:
                return 'an unknown error occurred';

            default:
                return 'unknown api error occurred with code ' . $apiError;
        }
    }
}
