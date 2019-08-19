<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 16.12.2016
 * Time: 12:26
 */

namespace Famoser\XKCDCache\Tests\ServiceTests\Base;


use Famoser\XKCDCache\Framework\ContainerBase;
use Famoser\XKCDCache\Tests\Utils\TestHelper\ApiTestHelper;
use Famoser\XKCDCache\Tests\Utils\TestHelper\FrontendTestHelper;

/**
 * a base class used for testing the services
 * @package Famoser\XKCDCache\Tests\ServiceTests\Base
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
     * @return \Famoser\XKCDCache\Services\Interfaces\DatabaseServiceInterface
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