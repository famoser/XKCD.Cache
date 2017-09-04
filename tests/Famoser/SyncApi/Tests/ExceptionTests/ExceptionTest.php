<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 12.12.2016
 * Time: 13:47
 */

namespace Famoser\SyncApi\Tests\ExceptionTests;


use Famoser\SyncApi\Exceptions\ApiException;
use Famoser\SyncApi\Exceptions\FrontendException;
use Famoser\SyncApi\Exceptions\ServerException;
use Famoser\SyncApi\Types\DownloadStatus;
use Famoser\SyncApi\Types\FrontendError;
use Famoser\SyncApi\Types\ServerError;

/**
 * test if the exceptions conform
 * @package Famoser\SyncApi\Tests\ExceptionTests
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests that exception error messages & codes are correct
     */
    public function testTextConversionsCorrect()
    {
        $apiException = new ApiException(DownloadStatus::APPLICATION_NOT_FOUND);
        static::assertEquals(DownloadStatus::toString(DownloadStatus::APPLICATION_NOT_FOUND), $apiException->getMessage());
        static::assertEquals(DownloadStatus::APPLICATION_NOT_FOUND, $apiException->getCode());

        $frontendException = new FrontendException(FrontendError::ACCESS_DENIED);
        static::assertEquals(FrontendError::toString(FrontendError::ACCESS_DENIED), $frontendException->getMessage());
        static::assertEquals(FrontendError::ACCESS_DENIED, $frontendException->getCode());

        $serverException = new ServerException(ServerError::DATABASE_SAVE_FAILURE);
        static::assertEquals(ServerError::toString(ServerError::DATABASE_SAVE_FAILURE), $serverException->getMessage());
        static::assertEquals(ServerError::DATABASE_SAVE_FAILURE, $serverException->getCode());
    }
}