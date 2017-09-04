<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13/11/2016
 * Time: 12:27
 */

namespace Famoser\XKCD\Cache\Exceptions;


use Famoser\XKCD\Cache\Types\FrontendError;

/**
 * if an exception while displaying a page in the frontend occurs, this exception typ eis thrown
 * @package Famoser\XKCD\Cache\Exceptions
 */
class FrontendException extends \Exception
{
    /**
     * FrontendException constructor.
     * @param string $frontendError
     */
    public function __construct($frontendError)
    {
        parent::__construct(FrontendError::toString($frontendError), $frontendError, null);
    }
}