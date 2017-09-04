<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 14:02
 */

namespace Famoser\XKCD\Cache\Services\Interfaces;


interface SettingServiceInterface
{
    /**
     * indicates if the application is in debug mode
     *
     * @return boolean
     */
    public function getDebugMode();

    /**
     * gets the path of the active database
     * if the database does not exist, a new one is created by copying the template database
     *
     * @return string
     */
    public function getDbPath();

    /**
     * gets the path of the database template. see at getDbPath what the database template is used for
     *
     * @return string
     */
    public function getDbTemplatePath();

    /**
     * directory which can be written to by libraries which need a cache
     * this directory will not persist in different versions of the application
     *
     * @return string
     */
    public function getCachePath();

    /**
     * get the log file path
     *
     * @return string
     */
    public function getLogFilePath();

    /**
     * path where twig templates are located
     *
     * @return string
     */
    public function getTemplatePath();

    /**
     * get the path which is exposed to the public
     *
     * @return string
     */
    public function getPublicPath();

    /**
     * get the path which is the root of the PSR namespace
     *
     * @return string
     */
    public function getSrcPath();

    /**
     * get the path where the xkcd images should be cached
     *
     * @return string
     */
    public function getImageCachePath();

    /**
     * get the path where the XKCD images are accessible from outside
     *
     * @return string
     */
    public function getImagePublicBasePath();

    /**
     * get the path where the zips should be cached
     *
     * @return string
     */
    public function getZipCachePath();

    /**
     * maximum number of downloaded images of one request to /refresh
     *
     * @return int
     */
    public function getMaxRefreshImages();
}