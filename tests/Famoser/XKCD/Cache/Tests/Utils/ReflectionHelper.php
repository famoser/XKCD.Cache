<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 05/09/2017
 * Time: 08:45
 */

namespace Famoser\XKCD\Cache\Tests\Utils;


class ReflectionHelper
{
    /**
     * get an array of instances of all the classes in this exact namespace
     *
     * @param string $nameSpace PSR namespace
     * @param string $srcPath the base path of the source files
     * @return array
     */
    public static function getClassInstancesInNamespace($nameSpace, $srcPath)
    {
        $filePath = str_replace("\\", DIRECTORY_SEPARATOR, $nameSpace);
        $res = [];
        foreach (glob($srcPath . DIRECTORY_SEPARATOR . $filePath . DIRECTORY_SEPARATOR . "*.php") as $filename) {
            $className = $nameSpace . "\\" . substr($filename, strrpos($filename, DIRECTORY_SEPARATOR) + 1, -4);
            $res[] = new $className();
        }
        return $res;
    }
}