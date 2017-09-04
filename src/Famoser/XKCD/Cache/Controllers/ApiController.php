<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\XKCD\Cache\Controllers;


use Famoser\XKCD\Cache\Controllers\Base\BaseController;
use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Models\Response\RefreshResponse;
use Famoser\XKCD\Cache\Models\Response\StatusResponse;
use Famoser\XKCD\Cache\Models\XKCD\XKCDJson;
use Famoser\XKCD\Cache\Types\DownloadStatus;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the api controller provides the two public api methods
 *
 * @package Famoser\XKCD\Cache\Controllers
 */
class ApiController extends BaseController
{
    /**
     * returns the newest XKCD comic
     * @param $number
     * @return bool
     * @throws ServerException
     */
    protected function cacheComic($number)
    {
        try {
            /* @var XKCDJson $myJsonObject */
            $myJsonObject = $this->getXKCDService()->getComic($number);
            return $this->getCacheService()->persistComic($myJsonObject);
        } catch (\Exception $ex) {
            $this->getLoggingService()->log("failed to cache comic: " . $ex);
        }
        return false;
    }

    private function getNewestCacheNumber()
    {
        $newestCache = $this->getCacheService()->getNewestComic();
        if ($newestCache instanceof Comic) {
            $newestCachedComic = $newestCache->num;
        } else {
            $newestCachedComic = 0;
        }
        return $newestCachedComic;
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
        $newestOnline = $this->getXKCDService()->getNewestComic();
        $newestCachedNumber = $this->getNewestCacheNumber();

        if ($newestCachedNumber < $newestOnline->num) {
            $maxIterations = 10;
            for ($i = $newestCachedNumber + 1; $i < $newestOnline->num && $maxIterations > 0; $i++) {
                $maxIterations--;
                $this->cacheComic($i);
            }
            $this->getCacheService()->createImageZip($newestOnline->num);
        }

        $refreshResponse = new RefreshResponse();

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
        $newestCachedNumber = $this->getNewestCacheNumber();
        $newestOnline = $this->getXKCDService()->getNewestComic();

        $apiInfo = new StatusResponse();
        $apiInfo->api_version = 1;
        $apiInfo->latest_image_cached = $newestCachedNumber;
        $apiInfo->latest_image_published = $newestOnline->num;
        $apiInfo->hot = $newestCachedNumber == $newestOnline->num;

        return $this->returnJson($response, $apiInfo);
    }
}
