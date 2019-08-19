<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.12.2016
 * Time: 08:45
 */

namespace Famoser\XKCDCache\Tests\ControllerTests\Base;


use Famoser\XKCDCache\Models\Response\RefreshResponse;
use Famoser\XKCDCache\Tests\Utils\AssertHelper;
use Famoser\XKCDCache\Tests\Utils\Mock\CacheServiceMock;
use Famoser\XKCDCache\Tests\Utils\Mock\SettingServiceMock;
use Famoser\XKCDCache\Tests\Utils\TestHelper\ApiTestHelper;
use Famoser\XKCDCache\Tests\Utils\TestHelper\TestHelper;

/**
 * a base class for all api tests
 *
 * @package Famoser\XKCDCache\Tests\ControllerTests\Base
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
     * test if refresh runs fully
     */
    protected function checkFullRefresh()
    {
        //arrange
        $this->getTestHelper()->mockFullRequest("1.0/refresh");
        $this->mockRefreshServices();

        //act
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulApiResponse($this, $response);

        //assert
        /* @var RefreshResponse $responseObj */
        $responseObj = json_decode($responseStr);
        static::assertTrue($responseObj->refresh_pending);
        static::assertEquals(3, $responseObj->refresh_cap);
        static::assertEquals(3, $responseObj->refresh_count);
        static::assertEmpty($responseObj->missing_json, var_dump($responseObj->missing_json));
        static::assertEmpty($responseObj->missing_images, var_dump($responseObj->missing_images));
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