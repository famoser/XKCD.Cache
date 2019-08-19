<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 05/09/2017
 * Time: 08:16
 */

namespace Famoser\XKCDCache\Tests\ControllerTests\Base;


use Famoser\XKCDCache\Tests\Utils\TestHelper\TestHelper;

class TestController  extends BaseTestController
{
    /**
     * return the test helper you want to use
     *
     * @return TestHelper
     */
    protected function constructTestHelper()
    {
        return new TestHelper();
    }

    /**
     * @return TestHelper
     */
    protected function getTestHelper()
    {
        return parent::getTestHelper();
    }
}