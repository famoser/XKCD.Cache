<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 16.12.2016
 * Time: 12:15
 */

namespace Famoser\XKCDCache\Tests\ServiceTests;


use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Services\Interfaces\DatabaseServiceInterface;
use Famoser\XKCDCache\Tests\ServiceTests\Base\BaseTestService;

/**
 * tests the database service
 * the tests in here are very basic, as it can be assumed all methods from DS already word due
 * a) simplicity or
 * b) heavy manuela testing or
 * c) heavy usage by the whole application
 *
 * @package Famoser\XKCDCache\Tests\ServiceTests
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

    /**
     * tests the update method
     */
    public function testUpdate()
    {
        $databaseService = $this->getDatabaseService();
        $comic = $this->insertTestComic($databaseService);

        $res = $databaseService->getSingleByIdFromDatabase(new Comic(), $comic->id);
        static::assertNotNull($res);

        $comic->num += 1;
        static::assertTrue($databaseService->saveToDatabase($comic));

        $res = $databaseService->getSingleByIdFromDatabase(new Comic(), $comic->id);
        static::assertNotNull($res);
        static::assertEquals($comic->num, $res->num);
    }

    /**
     * tests the execute methods
     */
    public function testExecute()
    {
        $databaseService = $this->getDatabaseService();
        $this->insertTestComic($databaseService);

        $res = $databaseService->executeAndCount("SELECT COUNT(*) FROM comics");
        static::assertTrue($res == 1);

        $res = $databaseService->countFromDatabase(new Comic());
        static::assertTrue($res == 1);

        static::assertTrue($databaseService->execute("DELETE FROM comics"));

        $res = $databaseService->executeAndCount("SELECT COUNT(*) FROM comics");
        static::assertTrue($res == 0);

        $res = $databaseService->countFromDatabase(new Comic());
        static::assertTrue($res == 0);
    }

    /**
     * tests the execute methods
     */
    public function testDelete()
    {
        $databaseService = $this->getDatabaseService();
        $comic = $this->insertTestComic($databaseService);

        $res = $databaseService->countFromDatabase(new Comic());
        static::assertTrue($res == 1);

        static::assertTrue($databaseService->deleteFromDatabase($comic));

        $res = $databaseService->countFromDatabase(new Comic());
        static::assertTrue($res == 0);
    }

    /**
     * tests the execute methods
     */
    public function testFetchEdgeCases()
    {
        $databaseService = $this->getDatabaseService();
        $comic = $this->insertTestComic($databaseService);

        $res = $databaseService->getFromDatabase(new Comic());
        static::assertTrue(count($res) == 1);

        $res = $databaseService->getFromDatabase(new Comic(), "id = :id", ["id" => $comic->id]);
        static::assertTrue(count($res) == 1);

        $res = $databaseService->getWithInFromDatabase(new Comic(), "id", [$comic->id], false, "num = :num", ["num" => $comic->num]);
        static::assertTrue(count($res) == 1);

        $res = $databaseService->getWithInFromDatabase(new Comic(), "id", [$comic->id], false, "num = :num", ["num" => $comic->num + 1]);
        static::assertTrue(count($res) == 0);
    }
}