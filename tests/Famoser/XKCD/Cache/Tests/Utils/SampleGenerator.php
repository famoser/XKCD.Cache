<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 15:11
 */

namespace Famoser\XKCD\Cache\Tests\Utils;


use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Models\XKCD\XKCDJson;
use Famoser\XKCD\Cache\Types\Downloader;
use Famoser\XKCD\Cache\Types\DownloadStatus;

class SampleGenerator
{
    /*
     * https://xkcd.com/1282/info.0.json
     *
     * {
           "month":"10",
           "num":1282,
           "link":"",
           "year":"2013",
           "news":"",
           "safe_title":"Monty Hall",
           "transcript":"[[A figure - Monty Hall - stands on stage, holding a microphone. There are three doors; two labelled \"A\" and \"C\", which are closed, and one that is being held open by Monty. There's a ramp to the right, down which a goat is being led by beret guy.]]\nBeret guy: ... and my yard has so much grass, and I'll teach you tricks, and...\nGoat: \u00c3\u00a2\u00c2\u0099\u00c2\u00a5\n\n{{Title text: A few minutes later, the goat from behind door C drives away in the car.}}",
           "alt":"A few minutes later, the goat from behind door C drives away in the car.",
           "img":"https://imgs.xkcd.com/comics/monty_hall.png",
           "title":"Monty Hall",
           "day":"25"
        }
     */

    /**
     * returns an example comic
     *
     * @return Comic
     */
    public static function getComicSample()
    {
        $comic = new Comic();
        $comic->num = 1282;
        $comic->filename = "monty_hall.jpg";
        $comic->img = "https://imgs.xkcd.com/comics/monty_hall.png";
        $comic->title = "Monty Hall";
        $comic->safe_title = "Monty Hall";
        $comic->transcript = "[[A figure - Monty Hall - stands on stage, holding a microphone. There are three doors; two labelled \"A\" and \"C\", which are closed, and one that is being held open by Monty. There's a ramp to the right, down which a goat is being led by beret guy.]]\nBeret guy: ... and my yard has so much grass, and I'll teach you tricks, and...\nGoat: \u00c3\u00a2\u00c2\u0099\u00c2\u00a5\n\n{{Title text: A few minutes later, the goat from behind door C drives away in the car.}}";
        $comic->alt = "A few minutes later, the goat from behind door C drives away in the car.";
        $comic->news = "";
        $comic->publish_date = strtotime("25.10.2013");
        $comic->downloaded_by = Downloader::VERSION_1;
        $comic->download_date_time = time();
        $comic->status = DownloadStatus::SUCCESSFUL;
        $comic->json = "{\"month\": \"10\", \"num\": 1282, \"link\": \"\", \"year\": \"2013\", \"news\": \"\", \"safe_title\": \"Monty Hall\", \"transcript\": \"[[A figure - Monty Hall - stands on stage, holding a microphone. There are three doors; two labelled \\\"A\\\" and \\\"C\\\", which are closed, and one that is being held open by Monty. There's a ramp to the right, down which a goat is being led by beret guy.]]\nBeret guy: ... and my yard has so much grass, and I'll teach you tricks, and...\nGoat: \u00c3\u00a2\u00c2\u0099\u00c2\u00a5\n\n{{Title text: A few minutes later, the goat from behind door C drives away in the car.}}\", \"alt\": \"A few minutes later, the goat from behind door C drives away in the car.\", \"img\": \"https://imgs.xkcd.com/comics/monty_hall.png\", \"title\": \"Monty Hall\", \"day\": \"25\"}";
        $comic->link = "";
        $comic->status_message = "";
        return $comic;
    }

    /**
     * returns XKCD json object
     *
     * @return XKCDJson
     */
    public static function getJsonSample()
    {
        return json_decode(static::getComicSample()->json);
    }
}