<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13/11/2016
 * Time: 12:29
 */

namespace Famoser\SyncApi\Types;


/**
 * used to distinguish frontend errors
 *
 * @package Famoser\SyncApi\Types
 */
class ServerError
{
    const CONNECTION_FAILED = 10;
    const CACHE_INACCESSIBLE = 11;
    const CACHING_FAILED = 11;

    /**
     * convert to string
     *
     * @param string $code
     * @return string
     */
    public static function toString($code)
    {
        switch ($code) {
            case static::CONNECTION_FAILED:
                return "could not connect to the XKCD server";
            case static::CACHE_INACCESSIBLE:
                return "could not access the cache";
            case static::CACHING_FAILED:
                return "could not cache the comic";
            default:
                return 'unknown error occurred with code ' . $code;
        }
    }
}