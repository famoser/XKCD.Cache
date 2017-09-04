<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.11.2016
 * Time: 19:20
 */

namespace Famoser\XKCD\Cache\Models\Communication\Response;


use Famoser\XKCD\Cache\Models\Communication\Response\Base\BaseResponse;

/**
 * the response to an AuthorizationRequest
 * @package Famoser\XKCD\Cache\Models\Communication\Response
 */
class RefreshResponse extends BaseResponse
{
    /* @var int[] the known missing images in the cache (because there was an error in adding them to the cache) */
    public $missing_images = [];

    /* @var int[] the known missing json in the cache (because there was an error in adding them to the cache) */
    public $missing_json = [];
}
