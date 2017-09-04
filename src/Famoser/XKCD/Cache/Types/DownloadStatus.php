<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 27.05.2016
 * Time: 13:23
 */

namespace Famoser\SyncApi\Types;

/**
 * any error which occurs by processing an api request
 *
 * @package Famoser\SyncApi\Types
 */
class DownloadStatus
{
    const SUCCESSFUL = 0;

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
            case self::UNKNOWN_ERROR:
                return 'an unknown error occurred';

            default:
                return 'unknown api error occurred with code ' . $apiError;
        }
    }
}
