<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\SyncApi\Controllers;


use Famoser\SyncApi\Controllers\Base\ApiRequestController;
use Famoser\SyncApi\Controllers\Base\BaseController;
use Famoser\SyncApi\Controllers\Base\FrontendController;
use Famoser\SyncApi\Exceptions\ServerException;
use Famoser\SyncApi\Models\ApiInformation;
use Famoser\SyncApi\Models\Communication\Response\RefreshResponse;
use Famoser\SyncApi\Models\Communication\Response\StatusResponse;
use Famoser\SyncApi\Models\Communication\Response\XKCDJson;
use Famoser\SyncApi\Models\Entities\Comic;
use Famoser\SyncApi\Types\Downloader;
use Famoser\SyncApi\Types\DownloadStatus;
use Famoser\SyncApi\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the public controller displays the index page & other pages accessible to everyone
 *
 * @package Famoser\SyncApi\Controllers
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
     * @return Comic
     * @throws ServerException
     */
    protected function getNewestCacheComic()
    {
        try {
            $dbService = $this->getDatabaseService();
            return $dbService->getSingleFromDatabase(new Comic(), null, null, "number DESC");
        } catch (\Exception $ex) {
            $this->getLoggingService()->log("failed to fetch comic from cache: " . $ex);
            throw new ServerException(ServerError::CACHE_INACCESSIBLE);
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
                $myJson = file_get_contents("https://xkcd.com/" . $number . "/info.0.json");
                /* @var XKCDJson $myJsonObject */
                $myJsonObject = json_decode($myJson);

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

                try {
                    file_put_contents($this->getLoggingService(), file_get_contents($comic->img));
                } catch (\Exception $ex) {

                }

            } catch (\Exception $ex) {
                $comic->status = DownloadStatus::UNKNOWN_ERROR;
                $this->getLoggingService()->log("could not download comic " . $number . ": " . $ex);
            }

            return $dbService->saveToDatabase($comic);
        } catch (\Exception $ex) {
            $this->getLoggingService()->log("failed to cache comic: " . $ex);
        }
        return false;
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
