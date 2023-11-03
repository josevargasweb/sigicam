<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //return "SSS";
        try{
            $this->middleware('guest', ['except' => 'logout']);
        }
        catch(Exception $e)
        {
            return $e;
        }
        
    }


    public function login(Request $request){

        

        try{
            if (Auth::attempt(['name' => $request->email, 'password' => $request->password])) {
                //return "OK";
                return redirect()->route('home');

            }
            else
            {
                return "NO";
            }
        }
        catch(Exception $e){
            return $e;
        }
        
          
        //return "LO";
    }
}
