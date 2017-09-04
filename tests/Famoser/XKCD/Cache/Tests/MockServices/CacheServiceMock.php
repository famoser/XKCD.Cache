<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 14:01
 */

namespace Famoser\XKCD\Cache\Tests\MockServices;


use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Models\Communication\Response\XKCDJson;
use Famoser\XKCD\Cache\Services\Base\BaseService;
use Famoser\XKCD\Cache\Services\Interfaces\CacheServiceInterface;

class CacheServiceMock extends BaseService implements CacheServiceInterface
{
    /**
     * creates a zip file of all the images contained in the image folder with the target number as filename
     *
     * @param $number
     * @return bool
     * @throws ServerException
     */
    public function createImageZip($number)
    {

    }

    /**
     * returns the newest XKCD comic
     *
     * @return Comic
     * @throws ServerException
     */
    public function getNewestComic()
    {
        // TODO: Implement getNewestComic() method.
    }

    /**
     * persists the passed XKCD comic
     *
     * @param XKCDJson $XKCDComic
     * @return bool
     * @throws ServerException
     */
    public function persistComic($XKCDComic)
    {
        // TODO: Implement persistComic() method.
    }
}