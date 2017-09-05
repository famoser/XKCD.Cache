<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 05/09/2017
 * Time: 08:54
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests\Base;


use Famoser\XKCD\Cache\Tests\Utils\TestHelper\TestHelper;

abstract class BaseTestController extends \PHPUnit_Framework_TestCase
{
    /* @var TestHelper $testHelper */
    private $testHelper;

    /**
     * FrontendTestController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->testHelper = $this->constructTestHelper();
    }

    /**
     * return the test helper you want to use
     *
     * @return TestHelper
     */
    abstract protected function constructTestHelper();

    /**
     * returns the TestHelper constructed with constructTestHelper
     * @return mixed
     */
    protected function getTestHelper()
    {
        return $this->testHelper;
    }

    /**
     * cleans test db etc
     */
    public function tearDown()
    {
        $this->testHelper->cleanEnvironment();
    }

}