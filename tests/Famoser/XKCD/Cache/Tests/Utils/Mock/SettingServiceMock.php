<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 05/09/2017
 * Time: 10:21
 */

namespace Famoser\XKCD\Cache\Tests\Utils\Mock;


use Famoser\XKCD\Cache\Services\Base\BaseService;
use Famoser\XKCD\Cache\Services\Interfaces\SettingServiceInterface;
use Famoser\XKCD\Cache\Services\SettingService;
use Interop\Container\ContainerInterface;

class SettingServiceMock extends SettingService implements SettingServiceInterface
{
    /* @var ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($container);
    }

    /**
     * overrides the key value pair in the settings
     *
     * @param $key
     * @param $value
     */
    public function override($key, $value)
    {
        $this->container->get(parent::getSettingKey())[$key] = $value;
    }
}