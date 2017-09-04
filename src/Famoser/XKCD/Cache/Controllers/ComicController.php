<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\XKCD\Cache\Controllers;


use Famoser\XKCD\Cache\Controllers\Base\FrontendController;
use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Types\DownloadStatus;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the comic controller allows to inspect all cached files, and allows to analyze errors
 *
 * @package Famoser\XKCD\Cache\Controllers
 */
class ComicController extends FrontendController
{
    /**
     * show all comics
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function index(Request $request, Response $response, $args)
    {
        $comics = $this->getDatabaseService()->getFromDatabase(new Comic(), null, null, "num");
        return $this->renderTemplate($response, 'comics/list', $args + ["comics" => $comics]);
    }

    /**
     * show all comics
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function failed(Request $request, Response $response, $args)
    {
        $comics = $this->getDatabaseService()->getFromDatabase(new Comic(), "status <> :status", ["status" => DownloadStatus::SUCCESSFUL], "num");
        return $this->renderTemplate($response, 'comics/list', $args + ["comics" => $comics]);
    }

    /**
     * show all comics
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function show(Request $request, Response $response, $args)
    {
        $comic = $this->getDatabaseService()->getSingleFromDatabase(new Comic(), $args["id"]);
        $imagePublicPath = $this->getSettingsArray()["image_public_base_path"] . "/" . $comic->filename;
        return $this->renderTemplate($response, 'comics/show', $args + ["comic" => $comic, "image_public_path" => $imagePublicPath]);
    }
}
