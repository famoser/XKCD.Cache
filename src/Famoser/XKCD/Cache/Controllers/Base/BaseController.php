<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 27.05.2016
 * Time: 14:23
 */

namespace Famoser\XKCD\Cache\Controllers\Base;


use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Models\Communication\Response\Base\BaseResponse;
use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Types\ServerError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * the base controller which provides access to the environment
 *
 * Class BaseController
 * @package Famoser\XKCD\Cache\Controllers\Base
 */
class BaseController extends ContainerBase
{
    /**
     * returns the newest XKCD comic
     * @return Comic
     * @throws ServerException
     */
    protected function getNewestCacheComic()
    {
        try {
            $dbService = $this->getDatabaseService();
            return $dbService->getSingleFromDatabase(new Comic(), null, null, "number DESC");
        } catch (\Exception $ex) {
            $this->getLoggingService()->log("failed to fetch comic from cache: " . $ex);
            throw new ServerException(ServerError::CACHE_INACCESSIBLE);
        }
    }

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
        return $this->returnRawJson($response, json_encode($responseContent));
    }

    /**
     * returns model as json
     *
     * @param  Response $response
     * @param string $json
     * @return Response
     */
    protected function returnRawJson(Response $response, $json)
    {
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * returns model as json
     *
     * @param  Response $response
     * @param $type
     * @param $filename
     * @param $length
     * @param $content
     * @return Response
     * @internal param string $json
     */
    protected function returnRawFile(Response $response, $type, $filename, $length, $content)
    {
        $response->withHeader('Content-Type', $type);
        $response->withHeader('Pragma', "public");
        $response->withHeader('Content-disposition:', 'attachment; filename=' . $filename);
        $response->withHeader('Content-Transfer-Encoding', 'binary');
        $response->withHeader('Content-Length', $length);
        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
