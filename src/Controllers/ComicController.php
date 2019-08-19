<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\XKCDCache\Controllers;


use Famoser\XKCDCache\Controllers\Base\FrontendController;
use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Types\DownloadStatus;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the comic controller allows to inspect all cached files, and allows to analyze errors
 *
 * @package Famoser\XKCDCache\Controllers
 */
class ComicController extends FrontendController
{
    /**
     * show all comics
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response)
    {
        $comics = $this->getDatabaseService()->getFromDatabase(new Comic(), null, null, "num");
        return $this->renderTemplate($response, 'comics/list', ["comics" => $comics]);
    }

    /**
     * show all comics
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function failed(/** @scrutinizer ignore-unused */ Request $request, Response $response)
    {
        $comics = $this->getDatabaseService()->getFromDatabase(new Comic(), "status <> " . DownloadStatus::SUCCESSFUL, [], "num");
        return $this->renderTemplate($response, 'comics/list', ["comics" => $comics]);
    }

    /**
     * show all comics
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws NotFoundException
     */
    public function show(Request $request, Response $response, $args)
    {
        $comic = $this->getDatabaseService()->getSingleByIdFromDatabase(new Comic(), $args["id"]);
        if (!$comic instanceof Comic) {
            throw new NotFoundException($request, $response);
        }
        $imagePublicPath = $this->getSettingService()->getImagePublicBasePath() . "/" . $comic->filename;
        return $this->renderTemplate($response, 'comics/show', $args + ["comic" => $comic, "image_public_path" => $imagePublicPath]);
    }
}
