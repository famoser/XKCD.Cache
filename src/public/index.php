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
$ds = DIRECTORY_SEPARATOR;
$oneUp = ".." . $ds;
$basePath = realpath(__DIR__ . "/" . $oneUp . $oneUp) . $ds;

$debugMode = file_exists(".dev");

require '..' . $ds . '..' . $ds . 'vendor' . $ds . 'autoload.php';

$app = new XKCDCacheApp(
    [
        SettingService::getSettingKey() => SettingService::generateRecommendedSettings($basePath, $debugMode)
    ]
);

$app->run();