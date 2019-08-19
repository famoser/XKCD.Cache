<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.12.2016
 * Time: 13:00
 */

namespace Famoser\XKCDCache\Tests\TypeTests;


use Famoser\XKCDCache\Types\Downloader;
use Famoser\XKCDCache\Types\DownloadStatus;
use Famoser\XKCDCache\Types\FrontendError;
use Famoser\XKCDCache\Types\ServerError;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * tests the error types
 *
 * @package Famoser\XKCDCache\Tests\TypeTests
 */
class ErrorTypesTest extends TestCase
{
    const ERROR_NAMESPACE = "Famoser\\XKCDCache\\Types\\";

    /**
     * tests that all error descriptions for the different download status are unique
     * @throws \ReflectionException
     */
    public function testAllDifferentDownloadStatusDescriptions()
    {
        $reflection = new ReflectionClass(static::ERROR_NAMESPACE . "DownloadStatus");
        $messages = [];
        //add default error description
        $messages[str_replace((string)(-1), "", DownloadStatus::toString(-1))] = true;
        //code must be in default error description
        static::assertStringContainsString("-1", DownloadStatus::toString(-1));
        foreach ($reflection->getConstants() as $constant) {
            $message = DownloadStatus::toString($constant);
            $message = str_replace((string)$constant, "", $message);
            static::assertFalse(key_exists($message, $messages), "not specified for " . $constant);
            $tableNames[$message] = true;
        }
    }

    /**
     * tests that all downloader descriptions are unique
     * @throws \ReflectionException
     */
    public function testAllDifferentDownloaderDescriptions()
    {
        $reflection = new ReflectionClass(static::ERROR_NAMESPACE . "Downloader");
        $messages = [];
        //add default error description
        $messages[str_replace((string)(-1), "", Downloader::toString(-1))] = true;
        //code must be in default error description
        static::assertStringContainsString("-1", Downloader::toString(-1));
        foreach ($reflection->getConstants() as $constant) {
            $message = Downloader::toString($constant);
            $message = str_replace((string)$constant, "", $message);
            static::assertFalse(key_exists($message, $messages), "not specified for " . $constant);
            $tableNames[$message] = true;
        }
    }

    /**
     * tests that all error descriptions for the different frontend errors are unique
     * @throws \ReflectionException
     */
    public function testAllDifferentFrontendErrorDescriptions()
    {
        $reflection = new ReflectionClass(static::ERROR_NAMESPACE . "FrontendError");
        $messages = [];
        //add default error description
        $messages[str_replace((string)(-1), "", FrontendError::toString(-1))] = true;
        //code must be in default error description
        static::assertStringContainsString("-1", FrontendError::toString(-1));
        foreach ($reflection->getConstants() as $constant) {
            $message = FrontendError::toString($constant);
            $message = str_replace((string)$constant, "", $message);
            static::assertFalse(key_exists($message, $messages), "not specified for " . $constant);
            $tableNames[$message] = true;
        }
    }

    /**
     * tests that all error descriptions for the different server errors are unique
     * @throws \ReflectionException
     */
    public function testAllDifferentServerErrorDescriptions()
    {
        $reflection = new ReflectionClass(static::ERROR_NAMESPACE . "ServerError");
        $messages = [];
        //add default error description
        $messages[ServerError::toString(-1)] = true;
        //code must be in default error description
        static::assertStringContainsString("-1", ServerError::toString(-1));
        foreach ($reflection->getConstants() as $constant) {
            $message = ServerError::toString($constant);
            $message = str_replace((string)$constant, "", $message);
            static::assertFalse(key_exists($message, $messages), "not specified for " . $constant);
            $tableNames[$message] = true;
        }
    }
}