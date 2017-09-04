<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\XKCD\Cache\Controllers;


use Famoser\XKCD\Cache\Controllers\Base\BaseController;
use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Models\Communication\Response\RefreshResponse;
use Famoser\XKCD\Cache\Models\Communication\Response\StatusResponse;
use Famoser\XKCD\Cache\Models\Communication\Response\XKCDJson;
use Famoser\XKCD\Cache\Models\Entities\Comic;
use Famoser\XKCD\Cache\Types\Downloader;
use Famoser\XKCD\Cache\Types\DownloadStatus;
use Famoser\XKCD\Cache\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;
use ZipArchive;

/**
 * the public controller displays the index page & other pages accessible to everyone
 *
 * @package Famoser\XKCD\Cache\Controllers
 */
class ApiController extends BaseController
{
    /**
     * returns the newest XKCD comic
     * @return XKCDJson
     * @throws ServerException
     */
    protected function getNewestOnlineComic()
    {
        try {
            $newestJson = file_get_contents("https://xkcd.com/info.0.json");
            return json_decode($newestJson);
        } catch (\Exception $ex) {
            $this->getLoggingService()->log("failed to fetch comic from xkcd: " . $ex);
            throw new ServerException(ServerError::CONNECTION_FAILED);
        }
    }

    /**
     * returns the newest XKCD comic
     * @param $number
     * @return bool
     * @throws ServerException
     */
    protected function cacheComic($number)
    {
        try {
            $dbService = $this->getDatabaseService();

            $comic = new Comic();
            $comic->num = $number;
            try {
                //download json
                $myJson = file_get_contents("https://xkcd.com/" . $number . "/info.0.json");
                /* @var XKCDJson $myJsonObject */
                $myJsonObject = json_decode($myJson);

                //construct comic
                $comic->status = DownloadStatus::SUCCESSFUL;
                $comic->link = $myJsonObject->link;
                $comic->news = $myJsonObject->news;
                $comic->transcript = $myJsonObject->transcript;
                $comic->safe_title = $myJsonObject->safe_title;
                $comic->alt = $myJsonObject->alt;
                $comic->img = $myJsonObject->img;
                $comic->title = $myJsonObject->title;
                $comic->publish_date = strtotime($myJsonObject->day . "." . $myJsonObject->month . "." . $myJsonObject->year);
                $comic->download_date_time = time();
                $comic->downloaded_by = Downloader::VERSION_1;
                $comic->json = $myJsonObject;
                $comic->filename = substr($comic->img, strrpos($comic->img, "/") + 1);

                try {
                    //download image
                    $contents = file_get_contents($comic->img);
                    file_put_contents($this->getSettingsArray()["image_cache_path"] . DIRECTORY_SEPARATOR . $comic->filename, $contents);
                } catch (\Exception $ex) {
                    $comic->status = DownloadStatus::IMAGE_DOWNLOAD_FAILED;
                    $this->getLoggingService()->log("could not download comic " . $number . ": " . $ex);
                }
            } catch (\Exception $ex) {
                $comic->status = DownloadStatus::JSON_DOWNLOAD_FAILED;
                $this->getLoggingService()->log("could not download json " . $number . ": " . $ex);
            }

            return $dbService->saveToDatabase($comic);
        } catch (\Exception $ex) {
            $this->getLoggingService()->log("failed to cache comic: " . $ex);
        }
        return false;
    }

    /**
     * creates a zip file of all the images with the target number as filename
     *
     * @param $targetNumber
     */
    protected function createImageZip($targetNumber)
    {
        $zip = new ZipArchive();
        $filename = $this->getSettingsArray()["zip_cache_path"] . DIRECTORY_SEPARATOR . $targetNumber . ".zip";

        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            $this->getLoggingService()->log("could not create zip file at " . $filename);
        }

        $zip->addGlob($this->getSettingsArray()["image_cache_path"] . DIRECTORY_SEPARATOR . "*");
        $this->getLoggingService()->log("created zip file with " . $zip->numFiles . " files. status: " . $zip->status);
        $zip->close();
    }

    /**
     * show basic info about this application
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function refresh(Request $request, Response $response, $args)
    {
        $newestCache = $this->getNewestCacheComic();
        $newestOnline = $this->getNewestOnlineComic();

        $refreshResponse = new RefreshResponse();
        for ($i = $newestCache->num; $i < $newestOnline->num; $i++) {
            $this->cacheComic($i);
        }
        $this->createImageZip($newestOnline->num);

        $failed = $this->getDatabaseService()->getFromDatabase(new Comic(), "status <> :status", ["status" => DownloadStatus::SUCCESSFUL]);
        foreach ($failed as $item) {
            $refreshResponse->missing_images[] = $item->num;
        }

        $failed = $this->getDatabaseService()->getFromDatabase(new Comic(), "json IS NULL");
        foreach ($failed as $item) {
            $refreshResponse->missing_json[] = $item->num;
        }

        return $this->returnJson($response, $refreshResponse);
    }

    /**
     * show api info as json. Should be enough to configure the C# library
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function status(Request $request, Response $response, $args)
    {
        $newestCache = $this->getNewestCacheComic();
        $newestOnline = $this->getNewestOnlineComic();

        $apiInfo = new StatusResponse();
        $apiInfo->api_version = 1;
        $apiInfo->latest_image_cached = $newestCache->num;
        $apiInfo->latest_image_published = $newestOnline->num;
        $apiInfo->hot = $newestCache->num == $newestOnline->num;

        return $this->returnJson($response, $apiInfo);
    }
}
