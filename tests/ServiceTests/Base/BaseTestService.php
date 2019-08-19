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
use PHPUnit\Framework\TestCase;

/**
 * a base class used for testing the services
 * @package Famoser\XKCDCache\Tests\ServiceTests\Base
 */
class BaseTestService extends TestCase
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

    public function setUp(): void
    {
        $this->testHelper = new ApiTestHelper();

    }

    public function tearDown(): void
    {
        $this->testHelper->cleanEnvironment();
    }
}