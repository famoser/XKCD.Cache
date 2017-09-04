<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 27.05.2016
 * Time: 14:23
 */

namespace Famoser\SyncApi\Controllers\Base;


use Famoser\SyncApi\Framework\ContainerBase;
use Famoser\SyncApi\Models\Communication\Request\Base\BaseRequest;
use Famoser\SyncApi\Models\Communication\Response\Base\BaseResponse;
use Famoser\SyncApi\Repositories\XKCDRepository;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the base controller which provides access to the environment
 *
 * Class BaseController
 * @package Famoser\SyncApi\Controllers\Base
 */
class BaseController extends ContainerBase
{
    /**
     * redirects to the route specified in $slug
     *
     * @param  Request $request
     * @param  Response $response
     * @param  string $slug
     * @return Response
     */
    protected function redirect(Request $request, Response $response, $slug)
    {
        $reqUri = $request->getUri()->withPath($this->getRouter()->pathFor($slug));
        return $response->withRedirect($reqUri);
    }

    /**
     * returns model as json
     *
     * @param  Response $response
     * @param  BaseResponse $responseContent
     * @return Response
     */
    protected function returnJson(Response $response, BaseResponse $responseContent)
    {
        $response->getBody()->write(json_encode($responseContent));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
