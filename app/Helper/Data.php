<?php
namespace App\Helper;

class Data {
    
    public $meta    = [
        'code' => 200
    ];
    public $data     = [];

    public function respond($data = [], $exception = null)
    {
        $this->data     = (object) $data;

        if($exception)
        {
            $this->exception($exception);
        }

        return response()->json($this);
    }

    public function exception($exception)
    {
        $reflection     = (new \ReflectionClass($exception));

        $this->meta['error_type']   = $reflection->getShortName();
        $this->meta['message']      = $exception->getMessage();

        if($reflection->hasMethod('getStatusCode'))
        {
            $this->meta['code']     = $exception->getStatusCode();
        }
        else
        {
            $this->meta['code']     = 500;
            
        }

        if($reflection->hasMethod('callbackMethod'))
        {
            $this->meta     = $exception->callbackMethod($this->meta);
        }
    }
}