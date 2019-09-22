<?php


namespace App\Exception;


/**
 * Class ApiException
 * @package App\Exception
 */
class ApiException extends \RuntimeException
{
    /**
     * ApiException constructor.
     * @param int $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        parent::__construct($message, $code, null);
    }
}