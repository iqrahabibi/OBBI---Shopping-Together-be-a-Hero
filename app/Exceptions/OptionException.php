<?php

namespace App\Exceptions;

use Exception;

class OptionException extends Exception
{
    protected $StatusCode   = 400;

    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{ $this->code }]: { $this->message }\n";
    }

    public function getStatusCode()
    {
        return $this->StatusCode;
    }
}
