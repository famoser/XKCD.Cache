<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\XKCDCache\Controllers;


use Exception;
use Famoser\XKCDCache\Controllers\Base\BaseController;
use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Exceptions\ServerException;
use Famoser\XKCDCache\Models\Response\RefreshResponse;
use Famoser\XKCDCache\Models\Response\StatusResponse;
use Famoser\XKCDCache\Models\XKCD\XKCDJson;
use Famoser\XKCDCache\Types\DownloadStatus;
use Famoser\XKCDCache\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the api controller provides the two public api methods
 *
 * @package Famoser\XKCDCache\Controllers
 */
class ApiController extends BaseController
{
    /**
     * returns the newest XKCD comic
     * @param $number
     * @return bool
     */
    private function cacheComic($number)
    {
        try {
            /* @var XKCDJson $myJsonObject */
            $myJsonObject = $this->getXKCDService()->getComic($number);
            return $this->getCacheService()->persistComic($myJsonObject);
        } catch (Exception $ex) {
            $this->getLoggingService()->log("failed to cache comic: " . $ex);
        }
        return false;
    }

    /**
     * the newest comic contained in cache
     *
     * @return int
     * @throws ServerException
     * @throws ServerException
     */
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
     * the newest comic available online
     *
     * @return int|bool
     * @throws ServerException
     * @throws ServerException
     */
    private function getNewestOnlineNumber()
    {
        $newestOnline = $this->getXKCDService()->getNewestComic();
        if ($newestOnline != null) {
            return $newestOnline->num;
        } else {
            $this->getLoggingService()->log("XKCD server not available");
            return false;
        }
    }

    /**
     * show basic info about this application
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServerException
     */
    public function refresh(/** @scrutinizer ignore-unused */ Request $request, Response $response)
    {
        return $this->doRefresh($response);
    }

    /**
     * @param Response $response
     * @param bool $noLimit
     * @return Response
     * @throws ServerException
     */
    protected function doRefresh(Response $response, $noLimit = false)
    {
        $newestOnlineNumber = $this->getNewestOnlineNumber();
        if ($newestOnlineNumber === false) {
            throw new ServerException(ServerError::XKCD_CONNECTION_FAILED);
        }

        $newestCachedNumber = $this->getNewestCacheNumber();

        $refreshCount = 0;
        if ($newestCachedNumber < $newestOnlineNumber) {
            $maxIterations = $this->getSettingService()->getMaxRefreshImages();
            if ($noLimit) {
                $maxIterations = 1000;
            }
            $newestCachedNumber++;
            for (; $newestCachedNumber < $newestOnlineNumber && $maxIterations > 0; $newestCachedNumber++) {
                $maxIterations--;
                $refreshCount++;
                $this->cacheComic($newestCachedNumber);
            }
            $this->getCacheService()->createImageZip($newestCachedNumber);
        }

        $refreshResponse = new RefreshResponse();
        $refreshResponse->refresh_count = $refreshCount;
        $refreshResponse->refresh_cap = $this->getSettingService()->getMaxRefreshImages();
        $refreshResponse->refresh_pending = $newestCachedNumber != $newestOnlineNumber;

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
     * show basic info about this application
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServerException
     */
    public function refreshNoLimit(/** @scrutinizer ignore-unused */ Request $request, Response $response)
    {
        return $this->doRefresh($response, true);
    }

    /**
     * show api info as json. Should be enough to configure the C# library
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServerException
     */
    public function status(/** @scrutinizer ignore-unused */ Request $request, Response $response)
    {
        $newestOnlineNumber = $this->getNewestOnlineNumber();
        if ($newestOnlineNumber === false) {
            throw new ServerException(ServerError::XKCD_CONNECTION_FAILED);
        }

        $newestCachedNumber = $this->getNewestCacheNumber();

        $apiInfo = new StatusResponse();
        $apiInfo->api_version = 1;
        $apiInfo->latest_image_cached = $newestCachedNumber;
        $apiInfo->latest_image_published = $newestOnlineNumber;
        $apiInfo->hot = $newestCachedNumber >= $newestOnlineNumber;

        return $this->returnJson($response, $apiInfo);
    }
}
