<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04.11.2016
 * Time: 19:20
 */

namespace Famoser\XKCD\Cache\Models\Communication\Response;


/**
 * the response to an AuthorizationRequest
 * @package Famoser\XKCD\Cache\Models\Communication\Response
 */
class XKCDJson
{
    /* @var string $month: month of publish date */
    public $month;

    /* @var string $day: day of publish date */
    public $day;

    /* @var string $year: year of publish date */
    public $year;

    /* @var int $num: comic number */
    public $num;

    /* @var string $num: image link, empty most of the time */
    public $link;

    /* @var string $news: unknown */
    public $news;

    /* @var string $safe_title: safe title of the comic, same as $title most of the time */
    public $safe_title;

    /* @var string $safe_title: transcript of the comic */
    public $transcript;

    /* @var string $alt: hover text of comic */
    public $alt;

    /* @var string $img: image url of the comic */
    public $img;

    /* @var string $title: title of the comic */
    public $title;
}
