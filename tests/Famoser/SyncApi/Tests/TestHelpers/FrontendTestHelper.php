<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13.12.2016
 * Time: 13:01
 */

namespace Famoser\SyncApi\Tests\TestHelpers;


use Famoser\SyncApi\Models\Entities\Comic;
use Famoser\SyncApi\Models\Entities\FrontendUser;
use Famoser\SyncApi\Services\SessionService;
use Famoser\SyncApi\Tests\TestHelpers\Base\BaseTestHelper;
use Slim\Http\Environment;

/**
 * helps to test the frontend
 * @package Famoser\SyncApi\Tests\TestHelpers
 */
class FrontendTestHelper extends BaseTestHelper
{
    /* @var Comic $testApplication */
    private $testApplication;
    /* @var FrontendUser $testUser */
    private $testUser;

    /**
     * @return Comic
     */
    public function getTestApplication()
    {
        return $this->testApplication;
    }

    /**
     * @return FrontendUser
     */
    public function getTestUser()
    {
        return $this->testUser;
    }

    /**
     * prepare the database if needed
     */
    protected function prepareDatabase()
    {
        //create test user
        $user = new FrontendUser();
        $user->email = "me@who.ch";
        $user->password = '$2y$10$JCOftRPxCBzPhcF8aJQu8OIBhNuoCgW9sVH7oPxKQ8MZS67CQl.uW'; //hallo welt
        $user->reset_key = "reset_key";
        $user->username = "me";
        $this->getDatabaseService()->saveToDatabase($user);
        $this->testUser = $user;

        //create test application
        $application = new Comic();
        $application->application_id = "test_id";
        $application->application_seed = 0;
        $application->description = "a test application created while running tests";
        $application->name = "Test Application";
        $application->release_date_time = time() - 1;
        $application->admin_id = $user->id;
        $this->getDatabaseService()->saveToDatabase($application);
        $this->testApplication = $application;
    }

    /**
     * performs a login of the user
     */
    public function loginUser()
    {
        $this->getSessionService()->setValue(SessionService::FRONTEND_USER_ID, $this->getTestUser()->id);
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