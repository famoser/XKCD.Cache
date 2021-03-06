<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.11.2016
 * Time: 16:49
 */

namespace Famoser\XKCDCache\Entities;

/*
CREATE TABLE 'applications' (
  'id'                INTEGER DEFAULT NULL PRIMARY KEY AUTOINCREMENT,
  'admin_id'          INTEGER DEFAULT NULL REFERENCES 'frontend_users' ('id'),
  'name'              TEXT    DEFAULT NULL,
  'description'       TEXT    DEFAULT NULL,
  'application_id'    TEST    DEFAULT NULL,
  'application_seed'  INT    DEFAULT NULL,
  'release_date_time' TEXT    DEFAULT NULL
);
*/

use Famoser\XKCDCache\Entities\Base\BaseEntity;
use Famoser\XKCDCache\Types\Downloader;
use Famoser\XKCDCache\Types\DownloadStatus;

/**
 * represents a comic from XKCD
 * @package Famoser\XKCDCache\Entities
 */
class Comic extends BaseEntity
{
    /* @var int $status */
    public $status;

    /* @var int $downloaded_by */
    public $downloaded_by;

    /* @var string $status_message */
    public $status_message;

    /* @var int $download_date_time type_of:timestamp */
    public $download_date_time;

    /* @var string $filename */
    public $filename;

    /* @var int $publish_date type_of:timestamp */
    public $publish_date;

    /* @var int $id */
    public $num;

    /* @var string $link */
    public $link;

    /* @var string $news */
    public $news;

    /* @var string $transcript */
    public $transcript;

    /* @var string $safe_title */
    public $safe_title;

    /* @var string $alt */
    public $alt;

    /* @var string $img */
    public $img;

    /* @var string $title */
    public $title;

    /* @var string $json */
    public $json;

    /**
     * converts the status to text
     *
     * @return string
     */
    public function getStatusAsText()
    {
        return DownloadStatus::toString($this->status);
    }

    /**
     * display the name of the downloader
     *
     * @return string
     */
    public function getDownloadedByAsText()
    {
        return Downloader::toString($this->downloaded_by);
    }

    /**
     * get the name of the table from the database
     *
     * @return string
     */
    public function getTableName()
    {
        return 'comics';
    }
}
