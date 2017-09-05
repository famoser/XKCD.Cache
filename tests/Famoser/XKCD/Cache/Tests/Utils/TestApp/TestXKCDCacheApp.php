<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 17.12.2016
 * Time: 13:56
 */

namespace Famoser\XKCD\Cache\Tests\Utils\TestApp;


use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Services\Interfaces\CacheServiceInterface;
use Famoser\XKCD\Cache\Services\Interfaces\SettingServiceInterface;
use Famoser\XKCD\Cache\XKCDCacheApp;
use Slim\Container;

class TestXKCDCacheApp extends XKCDCacheApp
{
    /**
     * makes application execution silent (no output in phpUnit console)
     * @param bool $silent
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function run($silent = false)
    {
        return parent::run(true);
    }

    /**
     * override the used setting service
     *
     * @param SettingServiceInterface $settingService
     */
    public function overrideSettingService(SettingServiceInterface $settingService)
    {
        $container[ContainerBase::SETTING_SERVICE_KEY] = function (Container $container) use ($settingService) {
            return $settingService;
        };
    }

    /**
     * override the used cache service
     *
     * @param CacheServiceInterface $cacheService
     */
    public function overrideCacheService(CacheServiceInterface $cacheService)
    {
        $container[ContainerBase::CACHE_SERVICE_KEY] = function (Container $container) use ($cacheService) {
            return $cacheService;
        };
    }
}