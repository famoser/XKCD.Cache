<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 17.12.2016
 * Time: 14:46
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Models\Communication\Entities\UserCommunicationEntity;
use Famoser\XKCD\Cache\Models\Communication\Request\AuthorizationRequest;
use Famoser\XKCD\Cache\Tests\ControllerTests\Base\ApiTestController;
use Famoser\XKCD\Cache\Tests\TestHelpers\AssertHelper;
use Famoser\XKCD\Cache\Tests\TestHelpers\SampleGenerator;
use Famoser\XKCD\Cache\Types\DownloadStatus;
use Slim\Http\Environment;

class ApiGeneralTest extends ApiTestController
{
    /**
     * checks that correct json is returned for wrong request method
     */
    public function testWrongRequestMethod()
    {
        $syncRequest = new AuthorizationRequest();
        $this->testHelper->authorizeRequest($syncRequest);

        //create user
        $user = new UserCommunicationEntity();
        $user->PersonalSeed = 621842297;
        SampleGenerator::createEntity($user);

        $syncRequest->UserEntity = $user;
        $syncRequest->UserId = $user->Id;

        $json = json_encode($syncRequest, JSON_PRETTY_PRINT);
        $this->testHelper->getTestApp()->overrideEnvironment(
            Environment::mock(
                [
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/1.0/auth/sync',
                    'MOCK_POST_DATA' => $json,
                    'SERVER_NAME' => 'localhost',
                    'CONTENT_TYPE' => 'application/json;charset=utf8'
                ]
            )
        );
        $response = $this->testHelper->getTestApp()->run();
        $responseString = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("not find", $responseString);
    }

    public function testWrongNote()
    {
        $syncRequest = new AuthorizationRequest();
        $this->testHelper->authorizeRequest($syncRequest);

        //create user
        $user = new UserCommunicationEntity();
        $user->PersonalSeed = 621842297;
        SampleGenerator::createEntity($user);

        $syncRequest->UserEntity = $user;
        $syncRequest->UserId = $user->Id;

        $json = json_encode($syncRequest, JSON_PRETTY_PRINT);
        $this->testHelper->getTestApp()->overrideEnvironment(
            Environment::mock(
                [
                    'REQUEST_METHOD' => 'POST',
                    'REQUEST_URI' => '/1.0/auth/typo',
                    'MOCK_POST_DATA' => $json,
                    'SERVER_NAME' => 'localhost',
                    'CONTENT_TYPE' => 'application/json;charset=utf8'
                ]
            )
        );
        $response = $this->testHelper->getTestApp()->run();
        AssertHelper::checkForFailedApiResponse($this, $response, DownloadStatus::NODE_NOT_FOUND);
    }
}