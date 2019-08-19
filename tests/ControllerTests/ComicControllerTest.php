<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15/12/2016
 * Time: 21:30
 */

namespace Famoser\XKCDCache\Tests\ControllerTests;


use Famoser\XKCDCache\Tests\ControllerTests\Base\FrontendTestController;
use Famoser\XKCDCache\Tests\Utils\AssertHelper;
use Famoser\XKCDCache\Types\Downloader;
use Famoser\XKCDCache\Types\DownloadStatus;

/**
 * test the public nodes
 * @package Famoser\XKCDCache\Tests\ControllerTests
 */
class ComicControllerTest extends FrontendTestController
{
    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testIndexNode()
    {
        //check no comics
        $this->getTestHelper()->mockFullRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString("no comics", $responseStr);

        //check one comic
        $this->getTestHelper()->insertComic(12);
        $this->getTestHelper()->mockFullRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString("your cached comics", $responseStr);
        static::assertStringContainsString("12", $responseStr);

        //check more comic
        $this->getTestHelper()->insertComic(312412);
        $this->getTestHelper()->insertComic(81273123);
        $this->getTestHelper()->mockFullRequest("comics/");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString("312412", $responseStr);
        static::assertStringContainsString("81273123", $responseStr);
    }

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testFailedNode()
    {
        //check no comics
        $this->getTestHelper()->mockFullRequest("comics/failed");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString("no comics", $responseStr);

        //check one comic
        $this->getTestHelper()->insertComic(872138147, DownloadStatus::SUCCESSFUL);
        $this->getTestHelper()->mockFullRequest("/comics/failed");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString("no comics", $responseStr);

        //check more comic
        $this->getTestHelper()->insertComic(312412, DownloadStatus::IMAGE_DOWNLOAD_FAILED);
        $this->getTestHelper()->insertComic(81273123, DownloadStatus::IMAGE_DOWNLOAD_FAILED);
        $this->getTestHelper()->insertComic(89123132, DownloadStatus::UNKNOWN_ERROR);
        $this->getTestHelper()->mockFullRequest("comics/failed");
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString("312412", $responseStr);
        static::assertStringContainsString("81273123", $responseStr);
        static::assertStringContainsString("89123132", $responseStr);
        static::assertTrue(strpos($responseStr, "872138147") === false);
    }

    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testShowNode()
    {
        //check missing comic
        $this->getTestHelper()->mockFullRequest("comics/show/1");
        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForFailedResponse($this, $response, 404);

        //check existing comic
        $num = 12314;
        $myComic = $this->getTestHelper()->insertComic($num);
        $this->getTestHelper()->mockFullRequest("/comics/show/" . $myComic->id);
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertStringContainsString((string)$myComic->num, $responseStr);
        static::assertStringContainsString(DownloadStatus::toString($myComic->status), $responseStr);
        static::assertStringContainsString(str_replace("\"","&quot;", $myComic->json), $responseStr);
        static::assertStringContainsString($myComic->status_message, $responseStr);
        static::assertStringContainsString($myComic->link, $responseStr);
        static::assertStringContainsString(date("d.m.Y H:i", $myComic->download_date_time), $responseStr);
        static::assertStringContainsString(date("d.m.Y", $myComic->publish_date), $responseStr);
        static::assertStringContainsString(Downloader::toString($myComic->downloaded_by), $responseStr);
        static::assertStringContainsString($myComic->alt, $responseStr);
        static::assertStringContainsString($myComic->news, $responseStr);
        static::assertStringContainsString($myComic->filename, $responseStr);
        static::assertStringContainsString($myComic->img, $responseStr);
        static::assertStringContainsString($myComic->title, $responseStr);
        static::assertStringContainsString($myComic->safe_title, $responseStr);
        static::assertStringContainsString($myComic->transcript, $responseStr);

        //check missing comic
        $this->getTestHelper()->mockFullRequest("comics/show/" . ($myComic->id + 1));
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