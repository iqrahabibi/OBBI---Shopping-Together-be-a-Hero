<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class WelcomeController extends Controller
{
    public function login(Request $request)
    {
        if($request->hasCookie('token')){
            return redirect('/home');
        }
        return view('welcome');
    }
}
