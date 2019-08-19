<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 16.12.2016
 * Time: 12:15
 */

namespace Famoser\XKCDCache\Tests\ServiceTests;


use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Tests\ServiceTests\Base\BaseTestService;
use Famoser\XKCDCache\Tests\Utils\SampleGenerator;

/**
 * tests the database service
 * the tests in here are very basic, as it can be assumed all methods from DS already word due
 * a) simplicity or
 * b) heavy manuela testing or
 * c) heavy usage by the whole application
 *
 * @package Famoser\XKCDCache\Tests\ServiceTests
 */
class CacheServiceTest extends BaseTestService
{
    /**
     * tests the getById method
     */
    public function testGetById()
    {
        $comic = SampleGenerator::getComicSample();

        $databaseService = $this->getDatabaseService();
        $databaseService->saveToDatabase($comic);

        $res = $databaseService->getSingleByIdFromDatabase(new Comic(), $comic->id);
        static::assertNotNull($res);

        $res = $databaseService->getSingleByIdFromDatabase(new Comic(), $comic->id + 1);
        static::assertNull($res);
    }
}