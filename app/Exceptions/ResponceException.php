<?php

namespace App\Exceptions;

use Exception;

class ResponceException extends Exception
{
    public $message;
    public $statusCode;
    public function __construct($message,$statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }
    public function getStatusCode():int
    {
        return $this->statusCode;
    }
}
