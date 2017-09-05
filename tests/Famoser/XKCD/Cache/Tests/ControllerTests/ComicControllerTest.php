<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:30
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Tests\ControllerTests\Base\FrontendTestController;
use Famoser\XKCD\Cache\Tests\Utils\AssertHelper;
use Famoser\XKCD\Cache\Types\Downloader;
use Famoser\XKCD\Cache\Types\DownloadStatus;

/**
 * test the public nodes
 * @package Famoser\XKCD\Cache\Tests\ControllerTests
 */
class ComicControllerTest extends FrontendTestController
{
    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testIndexNode()
    {
        //check no comics
        $this->getTestHelper()->mockRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("no comics", $responseStr);

        //check one comic
        $this->getTestHelper()->insertComic(12);
        $this->getTestHelper()->mockRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("your cached comics", $responseStr);
        static::assertContains("12", $responseStr);

        //check more comic
        $this->getTestHelper()->insertComic(312412);
        $this->getTestHelper()->insertComic(81273123);
        $this->getTestHelper()->mockRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("312412", $responseStr);
        static::assertContains("81273123", $responseStr);
    }

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testFailedNode()
    {
        //check no comics
        $this->getTestHelper()->mockRequest("comics/failed");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("no comics", $responseStr);

        //check one comic
        $this->getTestHelper()->insertComic(872138147, DownloadStatus::SUCCESSFUL);
        $this->getTestHelper()->mockRequest("/comics/failed");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("no comics", $responseStr);

        //check more comic
        $this->getTestHelper()->insertComic(312412, DownloadStatus::IMAGE_DOWNLOAD_FAILED);
        $this->getTestHelper()->insertComic(81273123, DownloadStatus::IMAGE_DOWNLOAD_FAILED);
        $this->getTestHelper()->insertComic(89123132, DownloadStatus::UNKNOWN_ERROR);
        $this->getTestHelper()->mockRequest("comics/failed");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("312412", $responseStr);
        static::assertContains("81273123", $responseStr);
        static::assertContains("89123132", $responseStr);
        static::assertTrue(strpos($responseStr, "872138147") === false);
    }

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testShowNode()
    {
        //check missing comic
        $this->getTestHelper()->mockRequest("comics/show/1");
        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForFailedResponse($this, $response, 404);

        //check existing comic
        $num = 12314;
        $myComic = $this->getTestHelper()->insertComic($num);
        $this->getTestHelper()->mockRequest("/comics/show/" . $myComic->id);
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains((string)$myComic->num, $responseStr);
        static::assertContains(DownloadStatus::toString($myComic->status), $responseStr);
        static::assertContains(str_replace("\"","&quot;", $myComic->json), $responseStr);
        static::assertContains($myComic->status_message, $responseStr);
        static::assertContains($myComic->link, $responseStr);
        static::assertContains(date("d.m.Y H:i", $myComic->download_date_time), $responseStr);
        static::assertContains(date("d.m.Y", $myComic->publish_date), $responseStr);
        static::assertContains(Downloader::toString($myComic->downloaded_by), $responseStr);
        static::assertContains($myComic->alt, $responseStr);
        static::assertContains($myComic->news, $responseStr);
        static::assertContains($myComic->filename, $responseStr);
        static::assertContains($myComic->img, $responseStr);
        static::assertContains($myComic->title, $responseStr);
        static::assertContains($myComic->safe_title, $responseStr);
        static::assertContains($myComic->transcript, $responseStr);

        //check missing comic
        $this->getTestHelper()->mockRequest("comics/show/" . ($myComic->id + 1));
        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForFailedResponse($this, $response, 404);
    }

    /**
     * get all public nodes which should be accessible (return html & no error code)
     *
     * @return string[]
     */
    protected function getPublicNodes()
    {
        $comic = $this->getTestHelper()->insertComic(2131);
        return ["comics/", "comics/failed", "comics/show/" . $comic->id];

    }
}