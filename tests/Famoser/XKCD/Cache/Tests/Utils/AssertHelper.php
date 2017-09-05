<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/12/2016
 * Time: 12:55
 */

namespace Famoser\XKCD\Cache\Tests\Utils;


use Famoser\XKCD\Cache\Tests\ControllerTests\Base\ApiTestController;
use Psr\Http\Message\ResponseInterface;

/**
 * helps asserting properties of response
 *
 * @package Famoser\XKCD\Cache\Tests
 */
class AssertHelper
{
    /**
     * extract the response string from a response object
     *
     * @param ResponseInterface $response
     * @return string
     */
    public static function getResponseString(ResponseInterface $response)
    {
        $response->getBody()->rewind();
        return $response->getBody()->getContents();
    }

    /**
     * check if request was successful
     * returns the tested response string
     *
     * @param \PHPUnit_Framework_TestCase $testingUnit
     * @param ResponseInterface $response
     * @return string
     */
    public static function checkForSuccessfulApiResponse(
        \PHPUnit_Framework_TestCase $testingUnit,
        ResponseInterface $response
    )
    {
        $responseString = static::getResponseString($response);

        //valid status code
        $testingUnit->assertEquals(200, $response->getStatusCode(), $responseString);

        //no error in json response
        $testingUnit->assertContains("\"successful\":true", $responseString);
        $testingUnit->assertContains("\"error_message\":null", $responseString);


        return $responseString;
    }

    /**
     * check if request was successful
     * returns the tested response string
     *
     * @param \PHPUnit_Framework_TestCase $testingUnit
     * @param ResponseInterface $response
     * @return string
     */
    public static function checkForSuccessfulResponse(
        \PHPUnit_Framework_TestCase $testingUnit,
        ResponseInterface $response
    )
    {
        $responseString = static::getResponseString($response);

        //valid status code
        $testingUnit->assertEquals(200, $response->getStatusCode());

        //no error in json response
        $testingUnit->assertNotContains("exception", $responseString);

        return $responseString;
    }

    /**
     * check if request was successful
     * returns the tested response string
     *
     * @param \PHPUnit_Framework_TestCase $testingUnit
     * @param ResponseInterface $response
     * @param $redirectCode
     * @param $expectedLink
     * @return string
     */
    public static function checkForRedirectResponse(
        \PHPUnit_Framework_TestCase $testingUnit,
        ResponseInterface $response,
        $redirectCode,
        $expectedLink
    )
    {
        //valid status code
        $testingUnit->assertEquals($redirectCode, $response->getStatusCode());

        //no error in json response
        $responseString = static::getResponseString($response);
        $testingUnit->assertNotContains("Exception", $responseString);
        $testingUnit::assertEmpty($responseString);
        $testingUnit::assertContains($expectedLink, $response->getHeaderLine("location"));

        return $responseString;
    }

    /**
     * check if request failed (code != 200)
     * returns the tested response string
     *
     * @param \PHPUnit_Framework_TestCase $testingUnit
     * @param ResponseInterface $response
     * @param int $expectedCode
     * @return string
     */
    public static function checkForFailedResponse(
        \PHPUnit_Framework_TestCase $testingUnit,
        ResponseInterface $response,
        $expectedCode
    )
    {
        //valid status code
        $testingUnit->assertEquals($expectedCode, $response->getStatusCode());

        return static::getResponseString($response);
    }

    /**
     * check if request was successful
     * returns the tested response string
     *
     * @param ApiTestController|\PHPUnit_Framework_TestCase $testingUnit
     * @param ResponseInterface $response
     * @param string $expectedError
     * @param int $expectedCode
     * @return string
     */
    public static function checkForFailedApiResponse(
        ApiTestController $testingUnit,
        ResponseInterface $response,
        $expectedError, $expectedCode = 500
    )
    {
        $responseString = static::getResponseString($response);

        //valid status code
        $testingUnit->assertEquals($expectedCode, $response->getStatusCode());

        //no error in json response
        $testingUnit->assertContains("\"successful\":false", $responseString);
        $testingUnit->assertContains($expectedError, $responseString);

        return $responseString;
    }
}