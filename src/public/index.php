<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 22/05/2016
 * Time: 22:40
 */

session_start();

use Famoser\SyncApi\XKCDCacheApp;

//generate base path
$ds = DIRECTORY_SEPARATOR;
$oneUp = ".." . $ds;
$basePath = realpath(__DIR__ . "/" . $oneUp . $oneUp) . $ds;

$debugMode = file_exists(".debug");

require '..' . $ds . '..' . $ds . 'vendor' . $ds . 'autoload.php';

$app = new XKCDCacheApp(
    \Famoser\XKCD\Cache\Helper\DefaultSettingsHelper::generateSettingArray($basePath, $debugMode)
);

$app->run();