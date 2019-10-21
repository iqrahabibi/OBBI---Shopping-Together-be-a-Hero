<?php

namespace App\Exceptions;

use Exception;

class OAuthExceptions extends Exception
{
    protected $StatusCode   = 410;
    protected $variable     = [];
    
    public function __construct($message, $variable, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);

        if(is_array($variable))
        {
            $this->variable    = array_merge($this->variable, $variable);
        }
        else
        {
            $this->variable[]  = $variable;   
        }
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
