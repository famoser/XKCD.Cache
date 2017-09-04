<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 03/12/2016
 * Time: 20:56
 */

namespace Famoser\XKCD\Cache\Framework;


use Famoser\XKCD\Cache\Services\Interfaces\DatabaseServiceInterface;
use Famoser\XKCD\Cache\Services\Interfaces\LoggingServiceInterface;
use Famoser\XKCD\Cache\XKCDCacheApp;
use Interop\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;

/**
 * resolves the classes distributed by the ContainerInterface
 *
 * @package Famoser\XKCD\Cache\Framework
 */
class ContainerBase
{
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
     * return the logging service
     *
     * @return LoggingServiceInterface
     */
    public function getLoggingService()
    {
        return $this->container->get(XKCDCacheApp::LOGGING_SERVICE_KEY);
    }

    /**
     * return the logger
     *
     * @return string[]
     */
    public function getSettingsArray()
    {
        return $this->container->get(XKCDCacheApp::SETTINGS_KEY);
    }

    /**
     * get database helper, used for database access
     *
     * @return DatabaseServiceInterface
     */
    public function getDatabaseService()
    {
        return $this->container->get(XKCDCacheApp::DATABASE_SERVICE_KEY);
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