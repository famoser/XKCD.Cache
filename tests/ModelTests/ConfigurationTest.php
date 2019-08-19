<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 10.12.2016
 * Time: 23:48
 */

namespace Famoser\XKCDCache\Tests\ModelTests;


use Famoser\XKCDCache\Entities\Base\BaseEntity;
use Famoser\XKCDCache\Tests\Utils\TestHelper\ApiTestHelper;

/**
 * tests the configuration of the models
 *
 * @package Famoser\XKCDCache\Tests\ModelTests
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testTableNamesUnique()
    {
        $testHelper = new ApiTestHelper();
        $classes = $testHelper->getClassInstancesInNamespace($this, "Famoser\\XKCD\\Cache\\Entities");

        $tableNames = [];
        /* @var BaseEntity[] $classes */
        foreach ($classes as $class) {
            static::assertArrayNotHasKey($class->getTableName(), $tableNames, "already used " . $class->getTableName());
            $tableNames[$class->getTableName()] = true;
        }
    }
}