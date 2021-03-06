<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 13:24
 */

namespace Famoser\XKCDCache\Services;


use Exception;
use Famoser\XKCDCache\Exceptions\ServerException;
use Famoser\XKCDCache\Models\XKCD\XKCDJson;
use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\XKCDServiceInterface;
use Famoser\XKCDCache\Types\ServerError;

class XKCDService extends BaseService implements XKCDServiceInterface
{
    /**
     * fetches the url from the specified URL
     *
     * @param $url
     * @return XKCDJson
     * @throws ServerException
     */
    protected function getComicFromUrl($url)
    {
        try {
            $newestJson = file_get_contents($url);
            return json_decode($newestJson);
        } catch (Exception $ex) {
            $this->getLoggingService()->log("failed to fetch comic from xkcd: " . $ex);
            throw new ServerException(ServerError::CONNECTION_FAILED);
        }
    }

    /**
     * returns the newest XKCD comic
     *
     * @param $number
     * @return XKCDJson
     * @throws ServerException
     */
    public function getComic($number)
    {
        return $this->getComicFromUrl("https://xkcd.com/" . $number . "/info.0.json");
    }

    /**
     * returns the XKCD comic with the specified number
     *
     * @return XKCDJson
     * @throws ServerException
     */
    public function getNewestComic()
    {
        return $this->getComicFromUrl("https://xkcd.com/info.0.json");
    }
}