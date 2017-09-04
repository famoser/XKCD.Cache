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
use Famoser\XKCD\Cache\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the download controller exposes the two downloadable files
 *
 * @package Famoser\XKCD\Cache\Controllers
 */
class DownloadController extends BaseController
{
    /**
     * show basic info about this application
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws ServerException
     */
    public function downloadZip(Request $request, Response $response, $args)
    {
        $newestComic = $this->getCacheService()->getNewestComic();
        if (!($newestComic instanceof Comic)) {
            throw new ServerException(ServerError::CACHE_EMPTY);
        }

        $newestZip = $this->getCacheService()->getNewestZip();
        if ($newestZip === false) {
            throw new ServerException(ServerError::ZIP_NOT_FOUND);
        }

        $fileSize = $this->getCacheService()->getFileSizeOfZip($newestZip);
        $fileContent = $this->getCacheService()->getContentOfZip($newestZip);
        return $this->returnRawFile($response, "application/zip", "xkcd_comics_" . $newestZip . ".zip", $fileSize, $fileContent);
    }

    /**
     * show api info as json. Should be enough to configure the C# library
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function downloadJson(Request $request, Response $response, $args)
    {
        $jsonStart = "[";
        $jsonEnd = "]";

        $entities = $this->getDatabaseService()->getFromDatabase(new Comic(), null, null, "num");
        foreach ($entities as $entity) {
            $jsonStart .= $entity->json . ",";
        }

        return $this->returnRawJson($response, substr($jsonStart, 0, -1) . $jsonEnd);
    }
}
