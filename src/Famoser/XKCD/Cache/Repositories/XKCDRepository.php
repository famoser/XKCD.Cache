<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 05.11.2016
 * Time: 20:21
 */

namespace Famoser\SyncApi\Repositories;

/*
    const AuthorizationCodeValidTime = 0;
    const DeviceAuthenticationRequired = 1;
    const AuthorizationCodeLength = 2;
*/

use Famoser\SyncApi\Models\Display\SettingModel;
use Famoser\SyncApi\Models\Entities\ApplicationSetting;
use Famoser\SyncApi\Services\Interfaces\DatabaseServiceInterface;
use Famoser\SyncApi\Types\SettingKeys;

/**
 * manages the settings of an application
 *
 * @package Famoser\SyncApi\Repositories
 */
class XKCDRepository
{
    private $helper;

    /**
     * SettingsRepository constructor.
     *
     * @param DatabaseServiceInterface $helper
     * @param $applicationId
     */
    public function __construct(DatabaseServiceInterface $helper, $applicationId)
    {
        $this->helper = $helper;
    }
}
