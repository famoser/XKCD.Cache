<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 28/11/2016
 * Time: 19:10
 */

namespace Famoser\XKCD\Cache;


use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Models\Communication\Response\Base\BaseResponse;
use Famoser\XKCD\Cache\Services\DatabaseService;
use Famoser\XKCD\Cache\Services\Interfaces\LoggingServiceInterface;
use Famoser\XKCD\Cache\Services\LoggingService;
use Famoser\XKCD\Cache\Types\ServerError;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

/**
 * the sync api application, in one neat class :)
 *
 * @package Famoser\XKCD\Cache
 */
class XKCDCacheApp extends App
{
    private $controllerNamespace = 'Famoser\\XKCD\\Cache\\Controllers\\';

    const DATABASE_SERVICE_KEY = 'databaseService';
    const LOGGING_SERVICE_KEY = 'loggingService';

    const SETTINGS_KEY = 'settings';

    /**
     * Create new application
     *
     * @param array $configuration an associative array of app settings
     * @throws InvalidArgumentException when no container is provided that implements ContainerInterface
     */
    public function __construct($configuration)
    {
        //$configuration
        $configuration = array_merge(
            [
                'displayErrorDetails' => false,
                'debug_mode' => false
            ],
            $configuration
        );

        //construct parent with container
        parent::__construct(
            $this->constructContainer(
                [
                    XKCDCacheApp::SETTINGS_KEY => $configuration
                ]
            )
        );

        //add middleware (none)

        //add routes
        $this->group('', $this->getWebAppRoutes());
        $this->group('/1.0', $this->getApiRoutes());
    }

    /**
     * override the environment (to mock requests for example)
     *
     * @param Environment $environment
     */
    public function overrideEnvironment(Environment $environment)
    {
        $this->getContainer()['environment'] = $environment;
    }

    /**
     * get the web app routes
     *
     * @return \Closure
     */
    private function getWebAppRoutes()
    {
        $controllerNamespace = $this->controllerNamespace;
        return function () use ($controllerNamespace) {
            $this->get('/', $controllerNamespace . 'PublicController:index')->setName('index');

            $this->group(
                '/comic',
                function () use ($controllerNamespace) {
                    $this->get('/', $controllerNamespace . 'ComicController:index')
                        ->setName('comic_index');

                    $this->get('/show/{id}', $controllerNamespace . 'ComicController:show')
                        ->setName('comic_show');

                    $this->get('/refresh/{id}', $controllerNamespace . 'ComicController:refresh')
                        ->setName('comic_new');
                }
            );
        };
    }

    /**
     * get the api routes
     *
     * @return \Closure
     */
    private function getApiRoutes()
    {
        $controllerNamespace = $this->controllerNamespace;

        return function () use ($controllerNamespace) {
            $this->get('/refresh', $controllerNamespace . 'ApiController:index')->setName('api_refresh');
            $this->get('/status', $controllerNamespace . 'ApiController:index')->setName('api_status');

            $this->group(
                '/comic',
                function () use ($controllerNamespace) {
                    $this->get('/', $controllerNamespace . 'ComicController:index')
                        ->setName('comic_index');

                    $this->get('/show/{id}', $controllerNamespace . 'ComicController:show')
                        ->setName('comic_show');

                    $this->get('/refresh/{id}', $controllerNamespace . 'ComicController:refresh')
                        ->setName('comic_new');
                }
            );
        };
    }

    /**
     * create the container
     *
     * @param $configuration
     * @return Container
     */
    private function constructContainer($configuration)
    {
        $container = new Container($configuration);

        //add handlers & services
        $this->addHandlers($container);
        $this->addServices($container);

        //add view
        $container['view'] = function (Container $container) {
            $view = new Twig(
                $container->get(XKCDCacheApp::SETTINGS_KEY)['template_path'],
                [
                    'cache' => $container->get(XKCDCacheApp::SETTINGS_KEY)['cache_path'],
                    'debug' => $container->get(XKCDCacheApp::SETTINGS_KEY)['debug_mode']
                ]
            );
            $view->addExtension(
                new TwigExtension(
                    $container['router'],
                    $container['request']->getUri()
                )
            );

            return $view;
        };

        return $container;
    }

    /**
     * add the error handlers to the container
     *
     * @param Container $container
     */
    private function addHandlers(Container $container)
    {
        $errorHandler = $this->createErrorHandlerClosure($container);

        //third argument: \Throwable
        $container['phpErrorHandler'] = $errorHandler;
        //third argument: \Exception
        $container['errorHandler'] = $errorHandler;

        $container['notAllowedHandler'] = $this->createNotFoundHandlerClosure($container, ServerError::METHOD_NOT_ALLOWED);
        $container['notFoundHandler'] = $this->createNotFoundHandlerClosure($container, ServerError::NODE_NOT_FOUND);
    }

    /**
     * checks if a specific request is done by the api library
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function isApiRequest(ServerRequestInterface $request)
    {
        return strpos($request->getUri()->getPath(), '/1.0/') === 0;
    }

    /**
     * creates a closure which has no third argument
     *
     * @param ContainerInterface $container
     * @param $apiError
     * @return \Closure
     */
    private function createNotFoundHandlerClosure(ContainerInterface $container, $apiError)
    {
        return function () use ($container, $apiError) {
            return function (ServerRequestInterface $request, ResponseInterface $response) use ($container, $apiError) {

                /* @var LoggingServiceInterface $logger */
                $logger = $container[XKCDCacheApp::LOGGING_SERVICE_KEY];
                $logger->log(
                    "[" . date("c") . "]: not found / not allowed " . $request->getUri()
                );

                if ($this->isApiRequest($request)) {
                    $resp = new BaseResponse();
                    $resp->successful = false;
                    $resp->error_message = ServerError::toString($apiError);
                    return $response->withStatus(500)->withJson($resp);
                }
                return $container['view']->render($response, 'public/not_found.html.twig', []);
            };
        };
    }

    /**
     * creates a closure which accepts \Exception and \Throwable as third argument
     *
     * @param ContainerInterface $cont
     * @return \Closure
     */
    private function createErrorHandlerClosure(ContainerInterface $cont)
    {
        return function () use ($cont) {
            return function (ServerRequestInterface $request, ResponseInterface $response, $error = null) use ($cont) {
                if ($error instanceof \Exception || $error instanceof \Throwable) {
                    $errorString = $error->getFile() . ' (' . $error->getLine() . ')\n' .
                        $error->getCode() . ': ' . $error->getMessage() . '\n' .
                        $error->getTraceAsString();
                } else {
                    $errorString = 'unknown error type occurred :/. Details: ' . print_r($error);
                }

                /* @var LoggingServiceInterface $logger */
                $logger = $cont[XKCDCacheApp::LOGGING_SERVICE_KEY];
                $logger->log(
                    "[" . date("c") . "]: " . $errorString
                );

                //return json if api request
                if ($this->isApiRequest($request)) {
                    $resp = new BaseResponse();
                    $resp->successful = false;
                    if ($errorString instanceof ServerException) {
                        $resp->error_message = $error->getMessage();
                    } else {
                        $resp->error_message = $errorString;
                    }
                    return $cont['response']->withStatus(500)->withJson($resp);
                } else {
                    //general error page
                    $args = [];
                    $args['error'] = $errorString;
                    return $cont['view']->render($response, 'public/server_error.html.twig', $args);
                }
            };
        };
    }

    /**
     * add all services to the container
     *
     * @param Container $container
     */
    private function addServices(Container $container)
    {
        $container[XKCDCacheApp::LOGGING_SERVICE_KEY] = function (Container $container) {
            return new LoggingService($container);
        };
        $container[XKCDCacheApp::DATABASE_SERVICE_KEY] = function (Container $container) {
            return new DatabaseService($container);
        };
    }
}
