<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 07.11.2016
 * Time: 11:26
 */

namespace Famoser\XKCDCache\Controllers\Base;


use Slim\Http\Response;

/**
 * a frontend controller displays information in the web application.
 * @package Famoser\XKCDCache\Controllers\Base
 */
class FrontendController extends BaseController
{
    /**
     * @param Response $response
     * @param string $path
     * @param $args
     * @return mixed
     */
    protected function renderTemplate(Response $response, $path, $args)
    {
        return $this->getView()->render($response, $path . '.html.twig', $args);
    }
}
