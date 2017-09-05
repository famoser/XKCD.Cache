<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:30
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Tests\ControllerTests\Base\FrontendTestController;

/**
 * test the public nodes
 * @package Famoser\XKCD\Cache\Tests\ControllerTests
 */
class PublicControllerTest extends FrontendTestController
{
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