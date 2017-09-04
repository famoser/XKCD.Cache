<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 28/11/2016
 * Time: 19:51
 */

namespace Famoser\XKCD\Cache\Tests\TestHelpers;


use Famoser\XKCD\Cache\Models\Communication\Request\Base\BaseRequest;
use Famoser\XKCD\Cache\Entities\Comic;
use Famoser\XKCD\Cache\Entities\Collection;
use Famoser\XKCD\Cache\Entities\ContentVersion;
use Famoser\XKCD\Cache\Entities\Device;
use Famoser\XKCD\Cache\Entities\User;
use Famoser\XKCD\Cache\Entities\UserCollection;
use Famoser\XKCD\Cache\Tests\TestHelpers\Base\BaseTestHelper;
use Famoser\XKCD\Cache\Types\ContentType;
use Slim\Http\Environment;

/**
 * helps preparing the test cases
 *
 * @package Famoser\XKCD\Cache\Tests
 */
class ApiTestHelper extends BaseTestHelper
{
    private $mockAlreadyCalled;

    /**
     * mock a json POST request
     * call app->run afterwards
     *
     * @param $relativeLink
     * @param bool $autoReset
     * @internal param BaseRequest $request
     */
    public function mockGetRequest($relativeLink, $autoReset = true)
    {
        if ($this->mockAlreadyCalled && $autoReset) {
            $this->resetApplication();
        }
        $this->mockAlreadyCalled = true;
        $this->getTestApp()->overrideEnvironment(
            Environment::mock(
                [
                    'REQUEST_URI' => '/' . $relativeLink,
                    'SERVER_NAME' => 'localhost'
                ]
            )
        );
    }

    /**
     * prepare the database if needed
     */
    protected function prepareDatabase()
    {
        // intentionally left blank
    }
}