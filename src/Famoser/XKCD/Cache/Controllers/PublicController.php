<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07/06/2016
 * Time: 17:54
 */

namespace Famoser\XKCD\Cache\Controllers;


use Famoser\XKCD\Cache\Controllers\Base\FrontendController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the public controller displays the index page & other pages accessible to everyone
 *
 * @package Famoser\XKCD\Cache\Controllers
 */
class PublicController extends FrontendController
{
    /**
     * show basic info about this application
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function index(Request $request, Response $response, $args)
    {
        return $this->renderTemplate($response, 'public/index', $args);
    }
}
