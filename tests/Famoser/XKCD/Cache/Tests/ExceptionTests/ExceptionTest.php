<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.12.2016
 * Time: 13:47
 */

namespace Famoser\XKCD\Cache\Tests\ExceptionTests;


use Famoser\XKCD\Cache\Exceptions\FrontendException;
use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Types\FrontendError;
use Famoser\XKCD\Cache\Types\ServerError;

/**
 * test if the exceptions conform
 * @package Famoser\XKCD\Cache\Tests\ExceptionTests
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests that exception error messages & codes are correct
     */
    public function testTextConversionsCorrect()
    {
        $frontendException = new FrontendException(FrontendError::ACCESS_DENIED);
        static::assertEquals(FrontendError::toString(FrontendError::ACCESS_DENIED), $frontendException->getMessage());
        static::assertEquals(FrontendError::ACCESS_DENIED, $frontendException->getCode());

        $serverException = new ServerException(ServerError::NODE_NOT_FOUND);
        static::assertEquals(ServerError::toString(ServerError::NODE_NOT_FOUND), $serverException->getMessage());
        static::assertEquals(ServerError::NODE_NOT_FOUND, $serverException->getCode());
    }
}