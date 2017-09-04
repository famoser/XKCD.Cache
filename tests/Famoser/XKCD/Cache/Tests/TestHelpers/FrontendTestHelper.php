<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/09/2017
 * Time: 09:42
 */

namespace Famoser\XKCD\Cache\Tests\TestHelpers;


use Famoser\XKCD\Cache\Entities\Application;
use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Entities\FrontendUser;
use Famoser\XKCD\Cache\Services\SessionService;
use Famoser\XKCD\Cache\Tests\TestHelpers\Base\BaseTestHelper;
use Slim\Http\Environment;

/**
 * helps to test the frontend
 * @package Famoser\XKCD\Cache\Tests\TestHelpers
 */
class FrontendTestHelper extends BaseTestHelper
{
    /**
     * prepare the database if needed
     */
    protected function prepareDatabase()
    {
        //create test comic
        $comic = new Comic();
        $comic->num = "12";
        $this->getDatabaseService()->saveToDatabase($comic);
    }

    private $mockAlreadyCalled;

    /**
     * mock a json POST request
     * call app->run afterwards
     *
     * @param $relativeLink
     * @param string|array $postData
     * if null, a GET request will be sent.
     * if array will be converted automatically to valid post data
     * @param bool $autoReset
     */
    public function mockRequest($relativeLink, $postData = null, $autoReset = true)
    {
        if ($this->mockAlreadyCalled && $autoReset) {
            $this->resetApplication();
        }
        $this->mockAlreadyCalled = true;

        if (is_array($postData)) {
            $posting = "";
            foreach ($postData as $key => $value) {
                $posting .= $key . "=" . urlencode($value) . "&";
            }
            $posting = substr($posting, 0, -1);
        } else {
            $posting = $postData;
        }

        if ($posting != null) {
            $this->getTestApp()->overrideEnvironment(
                Environment::mock(
                    [
                        'REQUEST_METHOD' => 'POST',
                        'REQUEST_URI' => '/' . $relativeLink,
                        'MOCK_POST_DATA' => $posting,
                        'SERVER_NAME' => 'localhost',
                        'CONTENT_TYPE' => 'application/x-www-form-urlencoded'
                    ]
                )
            );
        } else {
            $this->getTestApp()->overrideEnvironment(
                Environment::mock(
                    [
                        'REQUEST_URI' => '/' . $relativeLink,
                        'SERVER_NAME' => 'localhost'
                    ]
                )
            );
        }
    }
}