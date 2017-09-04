<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.11.2016
 * Time: 19:20
 */

namespace Famoser\SyncApi\Models\Communication\Response;


use Famoser\SyncApi\Models\Communication\Entities\DeviceCommunicationEntity;
use Famoser\SyncApi\Models\Communication\Entities\UserCommunicationEntity;
use Famoser\SyncApi\Models\Communication\Response\Base\BaseResponse;

/**
 * the response to an AuthorizationRequest
 * @package Famoser\SyncApi\Models\Communication\Response
 */
class StatusResponse extends BaseResponse
{
    /* @var bool $hot: returns true if all imagess with numbers smaller and equal to the latest published on XKCD are in the cache */
    public $hot = true;

    /* @var int $latest_image_published: returns the latest image published on XKCD */
    public $latest_image_published;

    /* @var int $latest_image_cached: returns the latest image contained in the cache */
    public $latest_image_cached;

    /* @var int $api_version: the version of the api */
    public $api_version = 1;
}
