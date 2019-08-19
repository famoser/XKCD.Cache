<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:30
 */

namespace Famoser\XKCDCache\Tests\ControllerTests;


use Famoser\XKCDCache\Tests\ControllerTests\Base\FrontendTestController;
use Famoser\XKCDCache\Tests\Utils\AssertHelper;

/**
 * test the public nodes
 * @package Famoser\XKCDCache\Tests\ControllerTests
 */
class PublicControllerTest extends FrontendTestController
{
    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testIndexNode()
    {
        //check with no comics
        $this->getTestHelper()->mockFullRequest("");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        //it must contain repo name & /comics link
        static::assertStringContainsString("famoser/XKCD.Cache", $responseStr);
        static::assertStringContainsString("/comics", $responseStr);

        //check with one & more comic
        $this->getTestHelper()->insertComic(12);
        $this->getTestHelper()->mockFullRequest("");
        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForSuccessfulResponse($this, $response);
    }

    /**
     * get all public nodes which should be accessible (return html & no error code)
     *
     * @return string[]
     */
    protected function getPublicNodes()
    {
        return [
            ""
        ];
    }
}