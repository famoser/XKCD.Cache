<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 13:24
 */

namespace Famoser\XKCDCache\Services\Interfaces;


use Famoser\XKCDCache\Exceptions\ServerException;
use Famoser\XKCDCache\Models\XKCD\XKCDJson;

interface XKCDServiceInterface
{
    /**
     * returns the newest XKCD comic
     *
     * @param $number
     * @return XKCDJson
     * @throws ServerException
     */
    public function getComic($number);


    /**
     * returns the XKCD comic with the specified number
     *
     * @return XKCDJson
     * @throws ServerException
     */
    public function getNewestComic();
}