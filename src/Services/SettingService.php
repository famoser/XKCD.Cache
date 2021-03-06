<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 14:03
 */

namespace Famoser\XKCDCache\Services;


use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\SettingServiceInterface;
use Interop\Container\ContainerInterface;

class SettingService extends BaseService implements SettingServiceInterface
{
    /* @var ContainerInterface $container */
    private $container;

    /**
     * SettingService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->container = $container;
    }

    /**
     * @param string $basePath
     * @param bool $debugMode
     * @param bool $testMode if set to true the database filename will be random
     * @return mixed[]
     */
    public static function generateRecommendedSettings(string $basePath, bool $debugMode, $testMode = false)
    {
        $ds = DIRECTORY_SEPARATOR;

        $appBasePath = $basePath . $ds . "app";
        $publicBasePath = $basePath . $ds . "public";

        if ($testMode) {
            $dbConfig = ['db_path' => $appBasePath . $ds . "data" . $ds . "data" . uniqid() . ".db"];
        } else {
            $dbConfig = ['db_path' => $appBasePath . $ds . "data" . $ds . "data.db"];
        }
        return [
                'displayErrorDetails' => $debugMode,
                'debug_mode' => $debugMode,
                'db_template_path' => $appBasePath . $ds . "data_templates" . $ds . "data_template.db",
                'file_path' => $appBasePath . $ds . "files",
                'cache_path' => $appBasePath . $ds . "cache",
                'src_path' => $basePath . $ds . "src",
                'log_file_path' => $appBasePath . $ds . "logs" . $ds . "log.log",
                'template_path' => $appBasePath . $ds . "templates",
                'public_path' => $publicBasePath,
                'image_cache_path' => $publicBasePath . $ds . "images" . $ds . "xkcd",
                'image_public_base_path' => "/images/xkcd",
                'zip_cache_path' => $publicBasePath . $ds . "zip",
                'max_refresh_images' => 10
            ] + $dbConfig;
    }

    /**
     * get the array with all the settings
     *
     * @return mixed[]
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

    /**
     * maximum number of downloaded images of one request to /refresh
     *
     * @return int
     */
    public function getMaxRefreshImages()
    {
        return $this->getSettingArray()["max_refresh_images"];
    }

    /**
     * get the path which is the root of the PSR namespace
     *
     * @return string
     */
    public function getSrcPath()
    {
        return $this->getSettingArray()["src_path"];
    }
}