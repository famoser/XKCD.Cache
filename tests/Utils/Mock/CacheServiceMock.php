<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 14:01
 */

namespace Famoser\XKCDCache\Tests\Utils\Mock;


use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Exceptions\ServerException;
use Famoser\XKCDCache\Models\XKCD\XKCDJson;
use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\CacheServiceInterface;

class CacheServiceMock extends BaseService implements CacheServiceInterface
{
    /* @var Comic[] $cache : key is number of comic, value is Comic */
    private $cache = [];

    /**
     * creates a zip file of all the images contained in the image folder with the target number as filename
     *
     * @param $number
     * @return bool
     */
    public function createImageZip($number)
    {
        return true;
    }

    /**
     * returns the newest XKCD comic
     *
     * @return Comic
     */
    public function getNewestComic()
    {
        if (count($this->cache) == 0) {
            return null;
        }

        $maxKey = max(array_keys($this->cache));
        return $this->cache[$maxKey];
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
        $this->cache[$XKCDComic->num] = $XKCDComic;
        return true;
    }

    /**
     * returns the file size of the zip with the specified number
     *
     * @param $number
     * @return int
     */
    public function getFileSizeOfZip($number)
    {
        return 20;
    }

    /**
     * returns the content of the zip with the specified number
     *
     * @param $number
     * @return mixed
     * @throws \Exception
     */
    public function getContentOfZip($number)
    {
        if ($number == 14) {
            return 1;
        } else {
            throw new \Exception("invalid call! This zip could not exist");
        }
    }

    /**
     * returns the number of the newest zip
     * returns false if none found
     *
     * @return int|false
     */
    public function getNewestZip()
    {
        return 14;
    }
}