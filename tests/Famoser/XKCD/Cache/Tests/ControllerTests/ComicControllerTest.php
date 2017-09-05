<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:30
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Tests\ControllerTests\Base\FrontendTestController;
use Famoser\XKCD\Cache\Tests\Utils\AssertHelper;

/**
 * test the public nodes
 * @package Famoser\XKCD\Cache\Tests\ControllerTests
 */
class ComicControllerTest extends FrontendTestController
{
    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testComicNode()
    {
        //check no comics
        $this->getTestHelper()->mockRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("no comics", $responseStr);

        //check one comic
        $this->getTestHelper()->insertComic(12);
        $this->getTestHelper()->mockRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("your cached comics", $responseStr);
        static::assertContains("12", $responseStr);

        //check more comic
        $this->getTestHelper()->insertComic(312412);
        $this->getTestHelper()->insertComic(81273123);
        $this->getTestHelper()->mockRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("312412", $responseStr);
        static::assertContains("81273123", $responseStr);
    }

    /**
     * get all public nodes which should be accessible (return html & no error code)
     *
     * @return string[]
     */
    protected function getPublicNodes()
    {
        $num = 3123;
        $this->getTestHelper()->insertComic($num);
        return ["comics/", "comics/failed", "comics/show/" . $num];

    }
}