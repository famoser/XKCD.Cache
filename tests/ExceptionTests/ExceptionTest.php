<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.12.2016
 * Time: 13:47
 */

namespace Famoser\XKCDCache\Tests\ExceptionTests;


use Famoser\XKCDCache\Exceptions\FrontendException;
use Famoser\XKCDCache\Exceptions\ServerException;
use Famoser\XKCDCache\Types\FrontendError;
use Famoser\XKCDCache\Types\ServerError;
use PHPUnit\Framework\TestCase;

/**
 * test if the exceptions conform
 * @package Famoser\XKCDCache\Tests\ExceptionTests
 */
class ExceptionTest extends TestCase
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