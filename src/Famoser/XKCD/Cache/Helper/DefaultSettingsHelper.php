<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 09:45
 */

namespace Famoser\XKCD\Cache\Helper;


class DefaultSettingsHelper
{
    public static function generateSettingArray($basePath, $debugMode)
    {
        $ds = DIRECTORY_SEPARATOR;

        $appBasePath = $basePath . "app" . $ds;
        $publicBasePath = $basePath . "src" . $ds . "public";

        return [
            'displayErrorDetails' => $debugMode,
            'debug_mode' => $debugMode,
            'db_path' => $appBasePath . "data" . $ds . "data.sqlite",
            'db_template_path' => $appBasePath . "data_templates" . $ds . "data_template.sqlite",
            'file_path' => $appBasePath . "files",
            'cache_path' => $appBasePath . "cache",
            'log_file_path' => $appBasePath . "logs" . $ds . "log.log",
            'template_path' => $appBasePath . "templates",
            'public_path' => $publicBasePath,
            'image_cache_path' => $publicBasePath . $ds . "images" . $ds . "xkcd",
            'image_public_base_path' => "/images/xkcd",
            'zip_cache_path' => $publicBasePath . $ds . "zip"
        ];
    }
}