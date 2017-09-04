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
use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Types\Downloader;
use Famoser\XKCD\Cache\Types\DownloadStatus;
use Famoser\XKCD\Cache\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;
use ZipArchive;

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
        $newestCache = $this->getCacheService()->getNewestComic();
        $newestOnline = $this->getXKCDService()->getNewestComic();

        $refreshResponse = new RefreshResponse();
        for ($i = $newestCache->num; $i < $newestOnline->num; $i++) {
            $this->cacheComic($i);
        }
        if ($newestCache->num < $newestOnline->num) {
            $this->getCacheService()->createImageZip($newestOnline->num);
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
        $newestCache = $this->getCacheService()->getNewestComic();
        $newestOnline = $this->getXKCDService()->getNewestComic();

        $apiInfo = new StatusResponse();
        $apiInfo->api_version = 1;
        $apiInfo->latest_image_cached = $newestCache->num;
        $apiInfo->latest_image_published = $newestOnline->num;
        $apiInfo->hot = $newestCache->num == $newestOnline->num;

        return $this->returnJson($response, $apiInfo);
    }
}
