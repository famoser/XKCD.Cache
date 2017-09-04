<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 09.12.2016
 * Time: 08:45
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests\Base;


use Famoser\XKCD\Cache\Tests\TestHelpers\ApiTestHelper;

/**
 * a base class for all api tests
 *
 * @package Famoser\XKCD\Cache\Tests\ControllerTests\Base
 */
class ApiTestController extends \PHPUnit_Framework_TestCase
{
    /* @var ApiTestHelper $testHelper */
    protected $testHelper;

    /**
     * ApiTestController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->testHelper = new ApiTestHelper();
    }

    /**
     * cleans the test environment
     */
    public function tearDown()
    {
        $this->testHelper->cleanEnvironment();
    }
}