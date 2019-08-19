<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 27.05.2016
 * Time: 13:23
 */

namespace Famoser\XKCDCache\Types;

/**
 * downloader versions
 *
 * @package Famoser\XKCDCache\Types
 */
class Downloader
{
    const VERSION_1 = 1;

    /**
     * the version of the downloader user
     *
     * @param $downloader
     * @return string
     */
    public static function toString($downloader)
    {
        switch ($downloader) {
            case self::VERSION_1:
                return 'version 1';
            default:
                return 'unknown version with id ' . $downloader;
        }
    }
}
