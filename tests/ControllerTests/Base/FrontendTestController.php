<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13.12.2016
 * Time: 12:48
 */

namespace Famoser\XKCDCache\Tests\ControllerTests\Base;


use Famoser\XKCDCache\Tests\Utils\AssertHelper;
use Famoser\XKCDCache\Tests\Utils\TestHelper\FrontendTestHelper;
use Famoser\XKCDCache\Tests\Utils\TestHelper\TestHelper;

/**
 * test frontend nodes
 * @package Famoser\XKCDCache\Tests\ControllerTests\Base
 */
abstract class FrontendTestController extends BaseTestController
{
    /**
     * return the test helper you want to use
     *
     * @return TestHelper
     */
    protected function constructTestHelper()
    {
        return new FrontendTestHelper();
    }

    /**
     * @return FrontendTestHelper
     */
    protected function getTestHelper()
    {
        return parent::getTestHelper();
    }

    /**
     * get all public nodes which should be accessible (return html & no error code)
     *
     * @return string[]
     */
    protected abstract function getPublicNodes();

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testPublicNodes()
    {
        $links = $this->getPublicNodes();

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
        $this->getTestHelper()->mockFullRequest($link);

        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertNotEmpty($responseStr);
    }
}