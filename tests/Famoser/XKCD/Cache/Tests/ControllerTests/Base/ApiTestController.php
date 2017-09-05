<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.12.2016
 * Time: 08:45
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests\Base;


use Famoser\XKCD\Cache\Tests\Utils\Mock\CacheServiceMock;
use Famoser\XKCD\Cache\Tests\Utils\Mock\SettingServiceMock;
use Famoser\XKCD\Cache\Tests\Utils\TestHelper\ApiTestHelper;
use Famoser\XKCD\Cache\Tests\Utils\TestHelper\TestHelper;

/**
 * a base class for all api tests
 *
 * @package Famoser\XKCD\Cache\Tests\ControllerTests\Base
 */
class ApiTestController extends BaseTestController
{
    /**
     * return the test helper you want to use
     *
     * @return TestHelper
     */
    protected function constructTestHelper()
    {
        return new ApiTestHelper();
    }

    /**
     * mocks the services which safe the comics & the setting service to speed up the /refresh call
     */
    protected function mockRefreshServices()
    {
        $container = $this->getTestHelper()->getTestApp()->getContainer();

        //inject mock setting service
        $settingService = new SettingServiceMock($container);
        $settingService->override("max_refresh_images", 3);
        $this->getTestHelper()->getTestApp()->overrideSettingService($settingService);

        $cacheService = new CacheServiceMock($container);
        $this->getTestHelper()->getTestApp()->overrideCacheService($cacheService);

    }

    /**
     * @return ApiTestHelper
     */
    protected function getTestHelper()
    {
        return parent::getTestHelper();
    }
}