<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 13:27
 */

namespace Famoser\XKCD\Cache\Services\Interfaces;


use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Exceptions\ServerException;
use Famoser\XKCD\Cache\Models\XKCD\XKCDJson;

interface CacheServiceInterface
{
    /**
     * creates a zip file of all the images contained in the image folder with the target number as filename
     *
     * @param $number
     * @return bool
     * @throws ServerException
     */
    public function createImageZip($number);

    /**
     * returns the file size of the zip with the specified number
     *
     * @param $number
     * @return int
     */
    public function getFileSizeOfZip($number);

    /**
     * returns the content of the zip with the specified number
     *
     * @param $number
     * @return mixed
     */
    public function getContentOfZip($number);

    /**
     * returns the number of the newest zip
     * returns false if none found
     *
     * @return int|false
     */
    public function getNewestZip();

    /**
     * returns the newest XKCD comic
     *
     * @return Comic
     * @throws ServerException
     */
    public function getNewestComic();

    /**
     * persists the passed XKCD comic
     *
     * @param XKCDJson $XKCDComic
     * @return bool
     * @throws ServerException
     */
    public function persistComic($XKCDComic);
}