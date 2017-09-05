<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:52
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Models\Response\RefreshResponse;
use Famoser\XKCD\Cache\Models\Response\StatusResponse;
use Famoser\XKCD\Cache\Services\SettingService;
use Famoser\XKCD\Cache\Tests\ControllerTests\Base\ApiTestController;
use Famoser\XKCD\Cache\Tests\Utils\AssertHelper;
use Famoser\XKCD\Cache\Tests\Utils\Mock\CacheServiceMock;
use Famoser\XKCD\Cache\Tests\Utils\Mock\SettingServiceMock;

/**
 * tests the api controller methods
 * @package Famoser\XKCD\Cache\Tests\ControllerTests
 */
class ApiControllerTest extends ApiTestController
{
    /**
     * tests the refresh method
     */
    public function testRefresh()
    {
        //cache empty initially
        $this->checkFullRefresh();

        //get status
        $this->checkLastCachedImage(3);

        //cache with three images initially
        $this->checkFullRefresh();

        //now 6 should be in cache
        $this->checkLastCachedImage(6);
    }

    /**
     * check how many images are in cache
     * @param $cachedImage
     */
    protected function checkLastCachedImage($cachedImage)
    {
        //get status
        $this->getTestHelper()->mockFullRequest("1.0/status");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulApiResponse($this, $response);
        /* @var StatusResponse $responseObj */
        $responseObj = json_decode($responseStr);
        static::assertFalse($responseObj->hot);
        static::assertEquals($responseObj->latest_image_cached, $cachedImage);
        static::assertTrue($responseObj->latest_image_published > 1000);
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
        static::assertEmpty($responseObj->missing_json);
        static::assertEmpty($responseObj->missing_images);
    }

    /**
     * tests the refresh method
     */
    public function testStatus()
    {
        //cache empty behaviour
        $this->getTestHelper()->mockFullRequest("1.0/status");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulApiResponse($this, $response);
        /* @var StatusResponse $responseObj */
        $responseObj = json_decode($responseStr);
        static::assertFalse($responseObj->hot);
        static::assertEquals($responseObj->latest_image_cached, 0);
        static::assertTrue($responseObj->latest_image_published > 1000);
        static::assertEquals($responseObj->api_version, 1);
    }
}