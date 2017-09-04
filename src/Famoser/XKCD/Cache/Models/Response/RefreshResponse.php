<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.11.2016
 * Time: 19:20
 */

namespace Famoser\XKCD\Cache\Models\Response;


use Famoser\XKCD\Cache\Models\Response\Base\BaseResponse;

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

    /* @var int $refresh_count how many images were downloaded in the refresh step */
    public $refresh_count = 0;

    /* @var int $refresh_cap how many images are max downloaded in each refresh step */
    public $refresh_cap = 0;

    /* @var boolean $refresh_pending true if further refresh calls are necessary to refresh cache */
    public $refresh_pending = false;
}
