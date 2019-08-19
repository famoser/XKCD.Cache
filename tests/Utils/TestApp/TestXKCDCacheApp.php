<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 17.12.2016
 * Time: 13:56
 */

namespace Famoser\XKCDCache\Tests\Utils\TestApp;


use Famoser\XKCDCache\Framework\ContainerBase;
use Famoser\XKCDCache\Services\Interfaces\CacheServiceInterface;
use Famoser\XKCDCache\Services\Interfaces\SettingServiceInterface;
use Famoser\XKCDCache\XKCDCacheApp;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;

class TestXKCDCacheApp extends XKCDCacheApp
{
    /**
     * makes application execution silent (no output in phpUnit console)
     * @param bool $silent
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     * @throws NotFoundException
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
        $container[ContainerBase::SETTING_SERVICE_KEY] = function () use ($settingService) {
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
        $container[ContainerBase::CACHE_SERVICE_KEY] = function () use ($cacheService) {
            return $cacheService;
        };
    }
}