<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 16.12.2016
 * Time: 12:15
 */

namespace Famoser\XKCD\Cache\Tests\ServiceTests;


use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Services\Interfaces\DatabaseServiceInterface;
use Famoser\XKCD\Cache\Tests\ServiceTests\Base\BaseTestService;

/**
 * tests the database service
 * the tests in here are very basic, as it can be assumed all methods from DS already word due
 * a) simplicity or
 * b) heavy manuela testing or
 * c) heavy usage by the whole application
 *
 * @package Famoser\XKCD\Cache\Tests\ServiceTests
 */
class DatabaseServiceTest extends BaseTestService
{
    /**
     * generate a test object to insert
     *
     * @param DatabaseServiceInterface $databaseService
     * @return Comic
     */
    protected function insertTestComic(DatabaseServiceInterface $databaseService)
    {
        $comic = new Comic();
        $comic->num = 12;
        $databaseService->saveToDatabase($comic);

        return $comic;
    }

    /**
     * tests the get by id method
     */
    public function testGetWithIn()
    {
        $databaseService = $this->getDatabaseService();
        $comic = $this->insertTestComic($databaseService);

        $res = $databaseService->getWithInFromDatabase(new Comic(), "num", [1, 2, $comic->num]);
        static::assertTrue(count($res) == 1);

        $res = $databaseService->getWithInFromDatabase(new Comic(), "id", [$comic->num + 1]);
        static::assertTrue(count($res) == 0);
    }

    /**
     * tests the getById method
     */
    public function testGetById()
    {
        $databaseService = $this->getDatabaseService();
        $comic = $this->insertTestComic($databaseService);

        $res = $databaseService->getSingleByIdFromDatabase(new Comic(), $comic->id);
        static::assertNotNull($res);

        $res = $databaseService->getSingleByIdFromDatabase(new Comic(), $comic->id + 1);
        static::assertNull($res);
    }
}