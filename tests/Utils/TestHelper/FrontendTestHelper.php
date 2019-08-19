<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 09:42
 */

namespace Famoser\XKCDCache\Tests\Utils\TestHelper;

use Famoser\XKCDCache\Tests\Utils\SampleGenerator;
use Famoser\XKCDCache\Types\DownloadStatus;

/**
 * helps to test the frontend
 * @package Famoser\XKCDCache\Tests\TestHelpers
 */
class FrontendTestHelper extends TestHelper
{
    /**
     * inserts sample comic with the specified number
     *
     * @param $num
     * @param int $status
     * @return \Famoser\XKCDCache\Entities\Comic
     */
    public function insertComic($num, $status = DownloadStatus::SUCCESSFUL)
    {
        $sample = SampleGenerator::getComicSample();
        $sample->num = $num;
        $sample->status = $status;
        $this->getDatabaseService()->saveToDatabase($sample);
        return $sample;
    }
}