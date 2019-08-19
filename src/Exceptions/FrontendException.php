<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 13/11/2016
 * Time: 12:27
 */

namespace Famoser\XKCDCache\Exceptions;


use Exception;
use Famoser\XKCDCache\Types\FrontendError;

/**
 * if an exception while displaying a page in the frontend occurs, this exception typ eis thrown
 * @package Famoser\XKCDCache\Exceptions
 */
class FrontendException extends Exception
{
    /**
     * FrontendException constructor.
     * @param int $frontendError
     */
    public function __construct(int $frontendError)
    {
        parent::__construct(FrontendError::toString($frontendError), $frontendError, null);
    }
}