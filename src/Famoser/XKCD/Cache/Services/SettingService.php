<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 14:03
 */

namespace Famoser\XKCD\Cache\Services;


use Famoser\XKCD\Cache\Services\Base\BaseService;
use Famoser\XKCD\Cache\Services\Interfaces\SettingServiceInterface;
use Interop\Container\ContainerInterface;

class SettingService extends BaseService implements SettingServiceInterface
{
    /* @var ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($container);
    }

    /**
     * @param string $basePath
     * @param boolean $debugMode
     * @return string[]
     */
    public static function generateRecommendedSettings($basePath, $debugMode)
    {
        $ds = DIRECTORY_SEPARATOR;

        $appBasePath = $basePath . "app" . $ds;
        $publicBasePath = $basePath . "src" . $ds . "public";

        return [
            'displayErrorDetails' => $debugMode,
            'debug_mode' => $debugMode,
            'db_path' => $appBasePath . "data" . $ds . "data.db",
            'db_template_path' => $appBasePath . "data_templates" . $ds . "data_template.db",
            'file_path' => $appBasePath . "files",
            'cache_path' => $appBasePath . "cache",
            'log_file_path' => $appBasePath . "logs" . $ds . "log.log",
            'template_path' => $appBasePath . "templates",
            'public_path' => $publicBasePath,
            'image_cache_path' => $publicBasePath . $ds . "images" . $ds . "xkcd",
            'image_public_base_path' => "/images/xkcd",
            'zip_cache_path' => $publicBasePath . $ds . "zip"
        ];
    }

    /**
     * get the array with all the settings
     *
     * @return string[]
     */
    protected function getSettingArray()
    {
        return $this->container->get($this->getSettingKey());
    }

    /**
     * indicates if the application is in debug mode
     *
     * @return boolean
     */
    public function getDebugMode()
    {
        return $this->getSettingArray()["debug_mode"];
    }

    /**
     * gets the path of the active database
     * if the database does not exist, a new one is created by copying the template database
     *
     * @return string
     */
    public function getDbPath()
    {
        return $this->getSettingArray()["db_path"];
    }

    /**
     * gets the path of the database template. see at getDbPath what the database template is used for
     *
     * @return string
     */
    public function getDbTemplatePath()
    {
        return $this->getSettingArray()["db_template_path"];
    }

    /**
     * directory which can be written to by libraries which need a cache
     * this directory will not persist in different versions of the application
     *
     * @return string
     */
    public function getCachePath()
    {
        return $this->getSettingArray()["cache_path"];
    }

    /**
     * get the log file path
     *
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->getSettingArray()["log_file_path"];
    }

    /**
     * path where twig templates are located
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->getSettingArray()["template_path"];
    }

    /**
     * get the path which is exposed to the public
     *
     * @return string
     */
    public function getPublicPath()
    {
        return $this->getSettingArray()["public_path"];
    }

    /**
     * get the path where the xkcd images should be cached
     *
     * @return string
     */
    public function getImageCachePath()
    {
        return $this->getSettingArray()["image_cache_path"];
    }

    /**
     * get the path where the XKCD images are accessible from outside
     *
     * @return string
     */
    public function getImagePublicBasePath()
    {
        return $this->getSettingArray()["image_public_base_path"];
    }

    /**
     * get the path where the zips should be cached
     *
     * @return string
     */
    public function getZipCachePath()
    {
        return $this->getSettingArray()["zip_cache_path"];
    }

    /**
     * get the key the settings are saved under in the container
     *
     * @return string
     */
    public static function getSettingKey()
    {
        return "settings";
    }
}