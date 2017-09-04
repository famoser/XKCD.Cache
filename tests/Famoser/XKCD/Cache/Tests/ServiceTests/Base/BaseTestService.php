<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 16.12.2016
 * Time: 12:26
 */

namespace Famoser\XKCD\Cache\Tests\ServiceTests\Base;


use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Tests\TestHelpers\ApiTestHelper;
use Famoser\XKCD\Cache\Tests\TestHelpers\FrontendTestHelper;

/**
 * a base class used for testing the services
 * @package Famoser\XKCD\Cache\Tests\ServiceTests\Base
 */
class BaseTestService extends \PHPUnit_Framework_TestCase
{
    /* @var FrontendTestHelper $testHelper */
    protected $testHelper;

    /**
     * @return ContainerBase
     */
    private function getContainerBase()
    {
        return new ContainerBase($this->testHelper->getTestApp()->getContainer());
    }

    /**
     * returns a ready to use database service
     *
     * @return \Famoser\XKCD\Cache\Services\Interfaces\DatabaseServiceInterface
     */
    protected function getDatabaseService()
    {
        return $this->getContainerBase()->getDatabaseService();
    }

    public function setUp()
    {
        $this->testHelper = new ApiTestHelper();

    }

    public function tearDown()
    {
        $this->testHelper->cleanEnvironment();
    }
}