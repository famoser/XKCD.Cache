<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 03/12/2016
 * Time: 20:56
 */

namespace Famoser\XKCD\Cache\Framework;


use Famoser\XKCD\Cache\Services\Interfaces\CacheServiceInterface;
use Famoser\XKCD\Cache\Services\Interfaces\DatabaseServiceInterface;
use Famoser\XKCD\Cache\Services\Interfaces\LoggingServiceInterface;
use Famoser\XKCD\Cache\Services\Interfaces\SettingServiceInterface;
use Famoser\XKCD\Cache\Services\Interfaces\XKCDServiceInterface;
use Interop\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

/**
 * resolves the classes distributed by the ContainerInterface
 *
 * @package Famoser\XKCD\Cache\Framework
 */
class ContainerBase
{
    const DATABASE_SERVICE_KEY = 'databaseService';
    const LOGGING_SERVICE_KEY = 'loggingService';
    const CACHE_SERVICE_KEY = 'cacheService';
    const XKCD_SERVICE_KEY = 'xkcdService';
    const SETTING_SERVICE_KEY = 'settingService';

    /* @var ContainerInterface $container */
    private $container;

    /**
     * RequestService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * get database helper, used for database access
     *
     * @return DatabaseServiceInterface
     */
    public function getDatabaseService()
    {
        return $this->container->get(static::DATABASE_SERVICE_KEY);
    }

    /**
     * return the logging service
     *
     * @return LoggingServiceInterface
     */
    public function getLoggingService()
    {
        return $this->container->get(static::LOGGING_SERVICE_KEY);
    }

    /**
     * get database helper, used for database access
     *
     * @return CacheServiceInterface
     */
    public function getCacheService()
    {
        return $this->container->get(static::CACHE_SERVICE_KEY);
    }

    /**
     * get database helper, used for database access
     *
     * @return XKCDServiceInterface
     */
    public function getXKCDService()
    {
        return $this->container->get(static::XKCD_SERVICE_KEY);
    }

    /**
     * get a service which allows access to settings
     *
     * @return SettingServiceInterface
     */
    public function getSettingService()
    {
        return $this->container->get(static::SETTING_SERVICE_KEY);
    }

    /**
     * get router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * get the view
     *
     * @return mixed
     */
    public function getView()
    {
        return $this->container->get('view');
    }
}