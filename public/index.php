<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/05/2016
 * Time: 22:40
 */

use Famoser\XKCDCache\Services\SettingService;
use Famoser\XKCDCache\XKCDCacheApp;

session_start();

//generate base path
$basePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

$debugMode = file_exists(".dev");

require '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$app = new XKCDCacheApp(
    [
        SettingService::getSettingKey() => SettingService::generateRecommendedSettings($basePath, $debugMode)
    ]
);

$app->run();