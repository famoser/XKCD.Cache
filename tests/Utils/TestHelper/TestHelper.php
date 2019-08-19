<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13.12.2016
 * Time: 12:55
 */

namespace Famoser\XKCDCache\Tests\Utils\TestHelper;


use Deployer\Component\Version\Exception\InvalidStringRepresentationException;
use Famoser\XKCDCache\Framework\ContainerBase;
use Famoser\XKCDCache\Services\Interfaces\SettingServiceInterface;
use Famoser\XKCDCache\Services\SettingService;
use Famoser\XKCDCache\Tests\Utils\ReflectionHelper;
use Famoser\XKCDCache\Tests\Utils\TestApp\TestXKCDCacheApp;
use Famoser\XKCDCache\XKCDCacheApp;
use Interop\Container\ContainerInterface;
use Slim\Http\Environment;

/**
 * helps to test a Slim application
 * @package Famoser\XKCDCache\Tests\TestHelpers\Base
 */
class TestHelper extends ContainerBase
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
    }

    /**
     * resets application to prepare for new request, but does not reset the database
     */
    public function resetApplication()
    {
        //clean output buffer
        ob_end_clean();
        //start again so phpunit does not throw risky exceptions
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
        $basePath = __DIR__ . DIRECTORY_SEPARATOR. ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;

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
        $res = ReflectionHelper::getClassInstancesInNamespace($nameSpace, $srcPath);
        $testCase::assertTrue(count($res) > 0);
        foreach ($res as $obj) {
            $testCase::assertTrue(is_object($obj));
        }
        return $res;
    }

    /**
     * returns the test application app
     *
     * @return TestXKCDCacheApp
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


    private $mockAlreadyCalled;

    /**
     * mock a json POST request
     * call app->run afterwards
     *
     * @param $relativeLink
     * @param string|array $postData
     * if null, a GET request will be sent.
     * if array will be converted automatically to valid post data
     * @param bool $autoReset
     */
    public function mockFullRequest($relativeLink, $postData = null, $autoReset = true)
    {
        if ($this->mockAlreadyCalled && $autoReset) {
            $this->resetApplication();
        }
        $this->mockAlreadyCalled = true;

        if (is_array($postData)) {
            $posting = "";
            foreach ($postData as $key => $value) {
                $posting .= $key . "=" . urlencode($value) . "&";
            }
            $posting = substr($posting, 0, -1);
        } else {
            $posting = $postData;
        }

        if ($posting != null) {
            $this->getTestApp()->overrideEnvironment(
                Environment::mock(
                    [
                        'REQUEST_METHOD' => 'POST',
                        'REQUEST_URI' => '/' . $relativeLink,
                        'MOCK_POST_DATA' => $posting,
                        'SERVER_NAME' => 'localhost',
                        'CONTENT_TYPE' => 'application/x-www-form-urlencoded'
                    ]
                )
            );
        } else {
            $this->getTestApp()->overrideEnvironment(
                Environment::mock(
                    [
                        'REQUEST_URI' => '/' . $relativeLink,
                        'SERVER_NAME' => 'localhost'
                    ]
                )
            );
        }
    }


    /**
     * mock a json POST request
     * call app->run afterwards
     *
     * @param $relativeLink
     * @param string|array $postData
     * if null, a GET request will be sent.
     * if array will be converted automatically to valid post data
     * @param bool $autoReset
     */
    public function mockRequest($relativeLink, $postData = null, $autoReset = true)
    {
        if ($this->mockAlreadyCalled && $autoReset) {
            $this->resetApplication();
        }
        $this->mockAlreadyCalled = true;

        if (is_array($postData)) {
            $posting = "";
            foreach ($postData as $key => $value) {
                $posting .= $key . "=" . urlencode($value) . "&";
            }
            $posting = substr($posting, 0, -1);
        } else {
            $posting = $postData;
        }

        if ($posting != null) {
            $this->getTestApp()->overrideEnvironment(
                Environment::mock(
                    [
                        'REQUEST_METHOD' => 'POST',
                        'REQUEST_URI' => '/' . $relativeLink,
                        'MOCK_POST_DATA' => $posting,
                        'SERVER_NAME' => 'localhost',
                        'CONTENT_TYPE' => 'application/x-www-form-urlencoded'
                    ]
                )
            );
        } else {
            $this->getTestApp()->overrideEnvironment(
                Environment::mock(
                    [
                        'REQUEST_URI' => '/' . $relativeLink,
                        'SERVER_NAME' => 'localhost'
                    ]
                )
            );
        }
    }
}