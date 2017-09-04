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
use Famoser\XKCD\Cache\Models\Entities\Comic;
use Famoser\XKCD\Cache\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the public controller displays the index page & other pages accessible to everyone
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
        $newestComic = $this->getNewestCacheComic();
        if (!($newestComic instanceof Comic)) {
            throw new ServerException(ServerError::CACHE_EMPTY);
        }

        $zipCachePath = $this->getSettingsArray()["zip_cache_path"] . DIRECTORY_SEPARATOR;
        $currentNum = $newestComic->num;
        do {
            $zipPath = $zipCachePath . $currentNum . ".zip";
            $zipExists = file_exists($zipPath);
        } while (!$zipExists && $currentNum-- > 0);

        if ($zipExists) {
            return $this->returnRawFile($response, "application/zip", "xkcd_comics.zip", filesize($zipPath), file_get_contents($zipPath));
        }

        throw new ServerException(ServerError::CACHE_EMPTY);
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
