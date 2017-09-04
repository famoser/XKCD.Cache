<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.11.2016
 * Time: 19:18
 */

namespace Famoser\SyncApi\Models\Communication\Response\Base;


use Famoser\SyncApi\Types\DownloadStatus;

/**
 * some properties which every response contains
 * @package Famoser\SyncApi\Models\Communication\Response\Base
 */
class BaseResponse
{
    /* @var bool $successful: if the response has been evaluated successfully */
    public $successful = true;

    /* @var string $error_message: an error message from the server which contains more info in case $successful is set to false */
    public $error_message;
}
