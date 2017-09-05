<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 28/11/2016
 * Time: 19:10
 */

namespace Famoser\XKCD\Cache;


use Famoser\XKCD\Cache\Controllers\ApiController;
use Famoser\XKCD\Cache\Controllers\ComicController;
use Famoser\XKCD\Cache\Controllers\DownloadController;
use Famoser\XKCD\Cache\Controllers\PublicController;
use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Models\Response\Base\BaseResponse;
use Famoser\XKCD\Cache\Services\CacheService;
use Famoser\XKCD\Cache\Services\DatabaseService;
use Famoser\XKCD\Cache\Services\Interfaces\LoggingServiceInterface;
use Famoser\XKCD\Cache\Services\LoggingService;
use Famoser\XKCD\Cache\Services\SettingService;
use Famoser\XKCD\Cache\Services\XKCDService;
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
    /**
     * Create new application
     *
     * @param array $configuration an associative array of app settings
     * @throws InvalidArgumentException when no container is provided that implements ContainerInterface
     */
    public function __construct($configuration)
    {
        //construct parent with container
        parent::__construct(
            $this->constructContainer(
                $configuration
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
        return function () {
            $this->get('/', PublicController::class . ':index')->setName('index');

            $this->group(
                '/comics',
                function () {
                    $this->get('/', ComicController::class . ':index')->setName('comics_index');
                    $this->get('/failed', ComicController::class . ':failed')->setName('comics_failed');

                    $this->get('/show/{id}', ComicController::class . ':show')->setName('comics_show');
                    $this->get('/refresh/{id}', ComicController::class . ':refresh')->setName('comic_new');
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
        return function () {
            $this->get('/refresh', ApiController::class . ':refresh')->setName('api_refresh');
            $this->get('/status', ApiController::class . ':status')->setName('api_status');

            $this->group(
                '/download',
                function () {
                    $this->get('/zip', DownloadController::class . ':downloadZip')->setName('download_zip');
                    $this->get('/json', DownloadController::class . ':downloadJson')->setName('download_json');
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

        //add services
        $this->addServices($container);

        //construct base container to get services now needed to configure other services
        $baseContainer = new ContainerBase($container);

        //add error handlers
        $this->addHandlers($container, $baseContainer);

        //add view
        $container['view'] = function (Container $container) use ($baseContainer) {
            $settings = $baseContainer->getSettingService();
            $view = new Twig(
                $settings->getTemplatePath(),
                [
                    'cache' => $settings->getCachePath(),
                    'debug' => $settings->getDebugMode()
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
     * @param ContainerBase $containerBase
     */
    private function addHandlers(Container $container, ContainerBase $containerBase)
    {
        $errorHandler = $this->createErrorHandlerClosure($container, $containerBase);

        //third argument: \Throwable
        $container['phpErrorHandler'] = $errorHandler;
        //third argument: \Exception
        $container['errorHandler'] = $errorHandler;

        $container['notAllowedHandler'] = $this->createNotFoundHandlerClosure($container, $containerBase, ServerError::METHOD_NOT_ALLOWED);
        $container['notFoundHandler'] = $this->createNotFoundHandlerClosure($container, $containerBase, ServerError::NODE_NOT_FOUND);
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
     * @param ContainerBase $containerBase
     * @param $apiError
     * @return \Closure
     */
    private function createNotFoundHandlerClosure(ContainerInterface $container, ContainerBase $containerBase, $apiError)
    {
        return function () use ($container, $apiError, $containerBase) {
            return function (ServerRequestInterface $request, ResponseInterface $response) use ($container, $apiError, $containerBase) {
                $response = $response->withStatus(404);

                /* @var LoggingServiceInterface $logger */
                $logger = $containerBase->getLoggingService();
                $logger->log(
                    "[" . date("c") . "]: not found / not allowed " . $request->getUri()
                );

                if ($this->isApiRequest($request)) {
                    $resp = new BaseResponse();
                    $resp->successful = false;
                    $resp->error_message = ServerError::toString($apiError);
                    return $response->withJson($resp);
                }
                return $container['view']->render($response, 'public/not_found.html.twig', []);
            };
        };
    }

    /**
     * creates a closure which accepts \Exception and \Throwable as third argument
     *
     * @param ContainerInterface $container
     * @param ContainerBase $containerBase
     * @return \Closure
     */
    private function createErrorHandlerClosure(ContainerInterface $container, ContainerBase $containerBase)
    {
        return function () use ($container, $containerBase) {
            return function (ServerRequestInterface $request, ResponseInterface $response, $error = null) use ($container, $containerBase) {
                $response = $response->withStatus(500);

                if ($error instanceof \Exception || $error instanceof \Throwable) {
                    $errorString = $error->getFile() . ' (' . $error->getLine() . ')\n' .
                        $error->getCode() . ': ' . $error->getMessage() . '\n' .
                        $error->getTraceAsString();
                } else {
                    $errorString = 'unknown error type occurred :/. Details: ' . print_r($error);
                }

                /* @var LoggingServiceInterface $logger */
                $logger = $containerBase->getLoggingService();
                $logger->log(
                    "[" . date("c") . "]: " . $errorString
                );

                //return json if api request
                if ($this->isApiRequest($request)) {
                    $resp = new BaseResponse();
                    $resp->successful = false;
                    if ($containerBase->getSettingService()->getDebugMode() || !($error instanceof ServerException)) {
                        $resp->error_message = $errorString;
                    } else {
                        $resp->error_message = $error->getMessage();
                    }
                    return $container['response']->withJson($resp);
                } else {
                    //general error page
                    $args = [];
                    if ($containerBase->getSettingService()->getDebugMode()) {
                        $args['error'] = $errorString;
                    } else {
                        $args['error'] = "";
                    }
                    return $container['view']->render($response, 'public/server_error.html.twig', $args);
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
        $container[ContainerBase::LOGGING_SERVICE_KEY] = function (Container $container) {
            return new LoggingService($container);
        };
        $container[ContainerBase::DATABASE_SERVICE_KEY] = function (Container $container) {
            return new DatabaseService($container);
        };
        $container[ContainerBase::SETTING_SERVICE_KEY] = function (Container $container) {
            return new SettingService($container);
        };
        $container[ContainerBase::XKCD_SERVICE_KEY] = function (Container $container) {
            return new XKCDService($container);
        };
        $container[ContainerBase::CACHE_SERVICE_KEY] = function (Container $container) {
            return new CacheService($container);
        };
    }
}
