<?php
namespace App\Helper;

use Illuminate\Http\Request;

class Form {
    
    public $request;

    public function required($request, ...$input)
    {
        $this->request  = $request;

        $this->required_check($input);

        return $this;
    }

    protected function required_check($input)
    {
        foreach($input as $value)
        {
            if(is_array($value))
            {
                $this->required_check($value);
            }
            else
            {
                if($this->request->input($value) == null)
                {
                    throw new \ParameterExceptions("parameter $value is required", $value);
                }
            }
        }
    }
}