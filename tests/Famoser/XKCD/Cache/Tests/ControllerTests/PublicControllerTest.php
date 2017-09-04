<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:30
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Tests\ControllerTests\Base\FrontendTestController;
use Famoser\XKCD\Cache\Tests\TestHelpers\AssertHelper;

/**
 * test the public nodes
 * @package Famoser\XKCD\Cache\Tests\ControllerTests
 */
class PublicControllerTest extends FrontendTestController
{
    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testPublicNodes()
    {
        $links = [
            "",
            "comics/"
        ];

        foreach ($links as $link) {
            $this->getValidHtmlResponse($link);
        }
    }

    /**
     * check if the corresponding relative link is behind the login wall
     *
     * @param $link
     */
    private function getValidHtmlResponse($link)
    {
        $this->getTestHelper()->mockRequest($link);

        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertNotEmpty($responseStr);
    }

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testInfoNode()
    {
        $this->getTestHelper()->mockRequest("info");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        $containerBase = new ContainerBase($this->getTestHelper()->getTestApp()->getContainer());
        static::assertContains((string)$containerBase->getSettingsArray()["api_modulo"], $responseStr);

        $jsonOb = json_decode($responseStr);
        static::assertNotNull($jsonOb);
    }

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function test404AndInvalidMethodNode()
    {
        $this->getTestHelper()->mockRequest("info", "postdata");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("not find", $responseStr);

        $this->getTestHelper()->mockRequest("23141", "postdata");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("not find", $responseStr);
    }
}