<?php

namespace App\Http\Exceptions;

use Exception;

class RiotApiException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
