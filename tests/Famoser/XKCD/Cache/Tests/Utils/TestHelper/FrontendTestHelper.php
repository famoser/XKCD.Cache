<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 09:42
 */

namespace Famoser\XKCD\Cache\Tests\Utils\TestHelper;

use Famoser\XKCD\Cache\Tests\Utils\SampleGenerator;

/**
 * helps to test the frontend
 * @package Famoser\XKCD\Cache\Tests\TestHelpers
 */
class FrontendTestHelper extends TestHelper
{
    /**
     * inserts sample comic with the specified number
     *
     * @param $num
     */
    public function insertComic($num)
    {
        $sample = SampleGenerator::getComicSample();
        $sample->num = $num;
        $this->getDatabaseService()->saveToDatabase($sample);
    }
}