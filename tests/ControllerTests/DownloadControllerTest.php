<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:52
 */

namespace Famoser\XKCDCache\Tests\ControllerTests;


use Famoser\XKCDCache\Controllers\ApiController;
use Famoser\XKCDCache\Models\Response\Base\BaseResponse;
use Famoser\XKCDCache\Models\Response\RefreshResponse;
use Famoser\XKCDCache\Models\Response\StatusResponse;
use Famoser\XKCDCache\Models\XKCD\XKCDJson;
use Famoser\XKCDCache\Services\SettingService;
use Famoser\XKCDCache\Tests\ControllerTests\Base\ApiTestController;
use Famoser\XKCDCache\Tests\Utils\AssertHelper;
use Famoser\XKCDCache\Tests\Utils\Mock\CacheServiceMock;
use Famoser\XKCDCache\Tests\Utils\Mock\SettingServiceMock;
use Famoser\XKCDCache\Types\ServerError;

/**
 * tests the api controller methods
 * @package Famoser\XKCDCache\Tests\ControllerTests
 */
class DownloadControllerTest extends ApiControllerTest
{
    /**
     * tests the refresh method
     */
    public function testDownloadZip()
    {
        //cache empty; so should receive error message not zip!
        $this->getTestHelper()->mockFullRequest("1.0/download/zip");
        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForFailedApiResponse($this, $response, ServerError::toString(ServerError::CACHE_EMPTY));

        //test with non-empty cache
        $this->getTestHelper()->mockFullRequest("1.0/refresh");
        $this->getTestHelper()->getTestApp()->run();
        $this->getTestHelper()->mockFullRequest("1.0/download/zip");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertNotEmpty($responseStr);
    }

    /**
     * tests the refresh method
     */
    public function testDownloadJson()
    {
        //cache empty; so should receive empty json array
        $this->getTestHelper()->mockFullRequest("1.0/download/json");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        /* @var XKCDJson[] $responseObj */
        $responseObj = json_decode($responseStr);
        static::assertEmpty($responseObj);

        //do a refresh
        $this->checkFullRefresh();

        //cache not empty; so should receive a non empty json array
        $this->getTestHelper()->mockFullRequest("1.0/download/json");
        $this->mockRefreshServices();
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        /* @var XKCDJson[] $responseObj */
        $responseObj = json_decode($responseStr);
        $cs = $this->getTestHelper()->getSettingService();
        static::assertTrue(count($responseObj) == $cs->getMaxRefreshImages());
    }

}