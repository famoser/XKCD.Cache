<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13/11/2016
 * Time: 12:29
 */

namespace Famoser\XKCD\Cache\Types;


/**
 * used to distinguish frontend errors
 *
 * @package Famoser\XKCD\Cache\Types
 */
class ServerError
{
    const CONNECTION_FAILED = 10;
    const CACHE_INACCESSIBLE = 11;
    const CACHING_FAILED = 12;
    const METHOD_NOT_ALLOWED = 13;
    const NODE_NOT_FOUND = 14;
    const CACHE_EMPTY = 15;

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
            case static::METHOD_NOT_ALLOWED:
                return "this method is now allowed";
            case static::NODE_NOT_FOUND:
                return "end node not found";
            default:
                return 'unknown error occurred with code ' . $code;
        }
    }
}