<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 15.12.2016
 * Time: 10:37
 */

namespace Famoser\XKCD\Cache\Tests\ControllerTests;


use Famoser\XKCD\Cache\Framework\ContainerBase;
use Famoser\XKCD\Cache\Entities\FrontendUser;
use Famoser\XKCD\Cache\Services\MailService;
use Famoser\XKCD\Cache\Services\SessionService;
use Famoser\XKCD\Cache\XKCDCacheApp;
use Famoser\XKCD\Cache\Tests\ControllerTests\Base\FrontendTestController;
use Famoser\XKCD\Cache\Tests\TestHelpers\AssertHelper;
use Famoser\XKCD\Cache\Tests\TestHelpers\MockServices\MockMailService;

/**
 * tests the login controller
 *
 * @package Famoser\XKCD\Cache\Tests\ControllerTests
 */
class LoginControllerTest extends FrontendTestController
{
    /**
     *  tests if all links return actual html, with no exceptions etc detectable
     */
    public function testAllRendering()
    {
        $links = [
            "login",
            "register",
            "forgot",
            "recover"
        ];

        foreach ($links as $link) {
            $this->getValidHtmlResponse($link);
        }
    }

    /**
     * check if the corresponding relative link responds html
     *
     * @param $link
     * @param string $method
     */
    private function getValidHtmlResponse($link, $method = "GET")
    {
        if ($method == "POST") {
            $this->getTestHelper()->mockRequest($link, "data=true");
        } else if ($method == "GET") {
            $this->getTestHelper()->mockRequest($link);
        } else {
            static::fail("invalid method specified");
        }
        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertNotEmpty($responseStr);
    }

    /**
     * tests the register function
     */
    public function testRegisterPost()
    {
        $usr = new FrontendUser();
        $usr->email = "emil@mymail.com";
        $usr->password = "password";
        $usr->reset_key = "reset_key";
        $usr->username = "usrname";
        $this->getTestHelper()->mockRequest("register",
            [
                "email" => $usr->email,
                "username" => $usr->username,
                "password" => $usr->password,
                "password2" => $usr->password
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForRedirectResponse($this, $response, 302, "login");

        $containerBase = new ContainerBase($this->getTestHelper()->getTestApp()->getContainer());
        $savedUser = $containerBase->getDatabaseService()->getSingleFromDatabase(
            new FrontendUser(),
            "email = :email AND username = :username",
            ["email" => $usr->email, "username" => $usr->username]
        );

        static::assertNotNull($savedUser);
        static::assertTrue(password_verify($usr->password, $savedUser->password));
    }

    /**
     * tests the register function: checks if double email / username can be added
     */
    public function testDoubleRegisterPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("register",
            [
                "email" => $usr->email,
                "username" => $usr->username . "0",
                "password" => $usr->password,
                "password2" => $usr->password
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForSuccessfulResponse($this, $response);

        $this->getTestHelper()->mockRequest("register",
            [
                "email" => $usr->email . "o",
                "username" => $usr->username,
                "password" => $usr->password,
                "password2" => $usr->password
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForSuccessfulResponse($this, $response);
    }

    /**
     * tests the register function; check if passwords are checked
     */
    public function testPasswordTypoRegisterPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("register",
            [
                "email" => $usr->email . "o",
                "username" => $usr->username . "o",
                "password" => $usr->password,
                "password2" => $usr->password . "o"
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForSuccessfulResponse($this, $response);
    }

    /**
     * tests the register function; check if passwords are checked
     */
    public function testLoginPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("login",
            [
                "username" => $usr->username,
                "password" => "hallo welt"
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForRedirectResponse($this, $response, 302, "dashboard/");

        //check if logged in correctly
        $containerBase = new ContainerBase($this->getTestHelper()->getTestApp()->getContainer());
        static::assertEquals(
            $containerBase->getSessionService()->getValue(SessionService::FRONTEND_USER_ID, -1),
            $usr->id
        );
    }

    /**
     * tests the register function; check if passwords are checked
     */
    public function testLoginFailedPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("login",
            [
                "username" => $usr->username,
                "password" => "hallo welt2"
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);

        static::assertContains("wrong", $responseStr);


        $this->getTestHelper()->mockRequest("login",
            [
                "username" => $usr->username . "a",
                "password" => "hallo welt"
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        $responseStr = AssertHelper::checkForSuccessfulResponse($this, $response);

        static::assertContains("wrong", $responseStr);
    }

    /**
     * tests the register function; check if passwords are checked
     */
    public function testForgotPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("forgot",
            [
                "username" => $usr->username,
                "email" => $usr->email
            ]);

        $mailService = new MockMailService();
        $this->getTestHelper()->getTestApp()->getContainer()[XKCDCacheApp::MAIL_SERVICE_KEY] = $mailService;

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForSuccessfulResponse($this, $response);


        $containerBase = new ContainerBase($this->getTestHelper()->getTestApp()->getContainer());
        $result = $containerBase->getDatabaseService()->getSingleFromDatabase(
            new FrontendUser(),
            "id = :id",
            ["id" => $usr->id]
        );

        static::assertNotNull($result);
        static::assertNotEquals($usr->reset_key, $result->reset_key);

        static::assertContains($result->reset_key, $mailService->getCache()["message"]);
        static::assertContains($usr->username, $mailService->getCache()["message"]);
        static::assertContains($usr->username, $mailService->getCache()["receiver"][$usr->email]);
    }

    /**
     * tests the recover function; check if passwords are checked
     */
    public function testRecoverPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("recover",
            [
                "username" => $usr->username,
                "authorization_code" => $usr->reset_key,
                "password" => "hallo welt 2",
                "password2" => "hallo welt 2",
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        AssertHelper::checkForRedirectResponse($this, $response, 302, "login");

        $containerBase = new ContainerBase($this->getTestHelper()->getTestApp()->getContainer());
        $result = $containerBase->getDatabaseService()->getSingleFromDatabase(
            new FrontendUser(),
            "id = :id",
            ["id" => $usr->id]
        );

        static::assertNotNull($result);
        static::assertNotEquals($usr->reset_key, $result->reset_key);
        static::assertNotEquals($usr->password, $result->password);
        static::assertTrue(password_verify("hallo welt 2", $result->password));
    }

    /**
     * tests the recover function; check if passwords are checked
     */
    public function testRecoverWrongPost()
    {
        $usr = $this->getTestHelper()->getTestUser();
        $this->getTestHelper()->mockRequest("recover",
            [
                "username" => $usr->username,
                "authorization_code" => $usr->reset_key."0",
                "password" => "hallo welt 2",
                "password2" => "hallo welt 2",
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        $resonseString = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("wrong", $resonseString);


        $this->getTestHelper()->mockRequest("recover",
            [
                "username" => $usr->username,
                "authorization_code" => $usr->reset_key,
                "password" => "hallo welt 2a",
                "password2" => "hallo welt 2",
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        $resonseString = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("wrong", $resonseString);

        $this->getTestHelper()->mockRequest("recover",
            [
                "username" => $usr->username."2",
                "authorization_code" => $usr->reset_key,
                "password" => "hallo welt 2",
                "password2" => "hallo welt 2",
            ]);

        $response = $this->getTestHelper()->getTestApp()->run();
        $resonseString = AssertHelper::checkForSuccessfulResponse($this, $response);
        static::assertContains("wrong", $resonseString);
    }
}