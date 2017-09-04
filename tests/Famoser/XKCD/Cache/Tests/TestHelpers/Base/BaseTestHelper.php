<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13.12.2016
 * Time: 12:55
 */

namespace Famoser\XKCD\Cache\Tests\TestHelpers\Base;


use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Services\SettingService;
use Famoser\XKCD\Cache\XKCDCacheApp;
use Famoser\XKCD\Cache\Tests\TestHelpers\TestApp\TestXKCDCacheApp;

/**
 * helps to test a Slim application
 * @package Famoser\XKCD\Cache\Tests\TestHelpers\Base
 */
abstract class BaseTestHelper extends ContainerBase
{

    /* @var XKCDCacheApp $testApp */
    private $testApp;
    /* @var array $config */
    private $config;

    /**
     * TestHelper constructor.
     */
    public function __construct()
    {
        //create config array
        $this->config = [SettingService::getSettingKey() => $this->constructConfig()];

        //create test app
        $this->testApp = new TestXKCDCacheApp($this->config);

        //use container to initialize parent
        parent::__construct($this->testApp->getContainer());

        //prepare environment
        $this->prepareDatabase();
    }

    /**
     * resets application to prepare for new request, but does not reset the database
     */
    public function resetApplication()
    {
        //clean output buffer
        ob_end_clean();
        //start again so phpunit does not throw risky exceptions (that motherf***er)
        ob_start();

        //dispose database service (free up database connection)
        $this->getDatabaseService()->dispose();

        //create test app
        $this->testApp = new TestXKCDCacheApp($this->config);

        //use container to initialize parent
        parent::__construct($this->testApp->getContainer());
    }

    /**
     * construct the configuration
     *
     * @return array
     */
    private function constructConfig()
    {
        $ds = DIRECTORY_SEPARATOR;
        $oneUp = ".." . $ds;
        $basePath = realpath(__DIR__ . "/" . $oneUp . $oneUp . $oneUp . $oneUp . $oneUp . $oneUp . $oneUp) . $ds;

        return SettingService::generateRecommendedSettings($basePath, true, true);
    }

    /**
     * get an array of instances of all the classes in this exact namespace
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param $nameSpace
     * @return array
     */
    public function getClassInstancesInNamespace(\PHPUnit_Framework_TestCase $testCase, $nameSpace)
    {
        $containerBase = new ContainerBase($this->getTestApp()->getContainer());
        $srcPath = $containerBase->getSettingService()->getSrcPath();
        $filePath = str_replace("\\", DIRECTORY_SEPARATOR, $nameSpace);
        $res = [];
        foreach (glob($srcPath . DIRECTORY_SEPARATOR . $filePath . DIRECTORY_SEPARATOR . "*.php") as $filename) {
            $className = $nameSpace . "\\" . substr($filename, strrpos($filename, DIRECTORY_SEPARATOR) + 1, -4);
            $res[] = new $className();
        }
        $testCase::assertTrue(count($res) > 0);
        foreach ($res as $obj) {
            $testCase::assertTrue(is_object($obj));
        }
        return $res;
    }

    /**
     * returns the test application app
     *
     * @return XKCDCacheApp
     */
    public function getTestApp()
    {
        return $this->testApp;
    }


    /**
     * cleans the environment, including database
     */
    public function cleanEnvironment()
    {
        $this->getDatabaseService()->dispose();

        $baseContainer = new ContainerBase($this->testApp->getContainer());
        $dbPath = $baseContainer->getSettingService()->getDbPath();
        //delete db if exists
        if (is_file($dbPath)) {
            unlink($dbPath);
        }
    }

    /**
     * prepare the database if needed
     */
    abstract protected function prepareDatabase();
}