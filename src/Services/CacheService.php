<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 13:29
 */

namespace Famoser\XKCDCache\Services;


use Exception;
use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Exceptions\ServerException;
use Famoser\XKCDCache\Models\XKCD\XKCDJson;
use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\CacheServiceInterface;
use Famoser\XKCDCache\Types\Downloader;
use Famoser\XKCDCache\Types\DownloadStatus;
use Famoser\XKCDCache\Types\ServerError;
use ZipArchive;

class CacheService extends BaseService implements CacheServiceInterface
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
        try {
            $zip = new ZipArchive();
            $filename = $this->getSettingService()->getZipCachePath() . DIRECTORY_SEPARATOR . $number . ".zip";

            if ($zip->open($filename, ZipArchive::CREATE) !== true) {
                $this->getLoggingService()->log("could not create zip file at " . $filename);
            }

            $zip->addGlob($this->getSettingService()->getImageCachePath() . DIRECTORY_SEPARATOR . "*");
            $this->getLoggingService()->log("created zip file with " . $zip->numFiles . " files. status: " . $zip->status);
            $zip->close();
            return true;
        } catch (Exception $ex) {
            $this->getLoggingService()->log("failed to create zip: " . $ex);
            throw new ServerException(ServerError::ZIP_FAILED);
        }
    }

    /**
     * returns the newest XKCD comic
     *
     * @return Comic
     */
    public function getNewestComic()
    {
        return $this->getDatabaseService()->getSingleFromDatabase(new Comic(), null, null, "num DESC");
    }

    /**
     * persists the passed XKCD comic
     *
     * @param XKCDJson $XKCDComic
     * @return bool
     */
    public function persistComic($XKCDComic)
    {
        $dbService = $this->getDatabaseService();

        //construct comic
        $comic = new Comic();
        $comic->num = $XKCDComic->num;
        $comic->status = DownloadStatus::SUCCESSFUL;
        $comic->link = $XKCDComic->link;
        $comic->news = $XKCDComic->news;
        $comic->transcript = $XKCDComic->transcript;
        $comic->safe_title = $XKCDComic->safe_title;
        $comic->alt = $XKCDComic->alt;
        $comic->img = $XKCDComic->img;
        $comic->title = $XKCDComic->title;
        $comic->publish_date = strtotime($XKCDComic->day . "." . $XKCDComic->month . "." . $XKCDComic->year);
        $comic->download_date_time = time();
        $comic->downloaded_by = Downloader::VERSION_1;
        $comic->json = json_encode($XKCDComic);
        $comic->filename = substr($comic->img, strrpos($comic->img, "/") + 1);

        try {
            //download image
            $contents = file_get_contents($comic->img);
            file_put_contents($this->getSettingService()->getImageCachePath() . DIRECTORY_SEPARATOR . $comic->filename, $contents);
        } catch (Exception $ex) {
            $comic->status = DownloadStatus::IMAGE_DOWNLOAD_FAILED;
            $this->getLoggingService()->log("could not download comic " . $XKCDComic->num . ": " . $ex);
        }

        return $dbService->saveToDatabase($comic);
    }

    /**
     * returns the path of the zip for this number
     *
     * @param $number
     * @return string
     */
    private function constructZipPath($number)
    {
        return $this->getSettingService()->getZipCachePath() . DIRECTORY_SEPARATOR . $number . ".zip";
    }

    /**
     * returns the file size of the zip with the specified number
     *
     * @param $number
     * @return int
     */
    public function getFileSizeOfZip($number)
    {
        $zipPath = $this->constructZipPath($number);
        return filesize($zipPath);
    }

    /**
     * returns the content of the zip with the specified number
     *
     * @param $number
     * @return mixed
     */
    public function getContentOfZip($number)
    {
        $zipPath = $this->constructZipPath($number);
        return file_get_contents($zipPath);
    }

    /**
     * returns the number of the newest zip
     * returns false if none found
     *
     * @return int|false
     * @throws ServerException
     * @throws ServerException
     */
    public function getNewestZip()
    {
        $newestComic = $this->getNewestComic();
        $currentNum = $newestComic->num;
        do {
            $zipPath = $this->constructZipPath($currentNum);
            $zipExists = file_exists($zipPath);
        } while (!$zipExists && $currentNum-- > 0);
        if ($zipExists) {
            return $currentNum;
        }
        return false;
    }
}