<?php

namespace App\Exceptions;

use Exception;

class DataNotFoundExceptions extends Exception
{
    protected $StatusCode   = 408;
    protected $variable;
    
    public function __construct($message, $variable, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->variable     = $variable;
    }

    public function __toString() {
        return __CLASS__ . ": [{ $this->code }]: { $this->message }\n";
    }

    public function getStatusCode()
    {
        return $this->StatusCode;
    }

    public function callbackMethod($meta)
    {
        $callback   = [
        ];

        return array_merge($meta, $callback);
    }
}
