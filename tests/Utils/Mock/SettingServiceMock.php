<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 05/09/2017
 * Time: 10:21
 */

namespace Famoser\XKCDCache\Tests\Utils\Mock;


use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\SettingServiceInterface;
use Famoser\XKCDCache\Services\SettingService;
use Interop\Container\ContainerInterface;

class SettingServiceMock extends SettingService implements SettingServiceInterface
{
    /* @var ContainerInterface $container */
    private $container;

    /**
     * SettingServiceMock constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->container = $container;
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