<?php

namespace App\Http\Controllers\Api;

use JwtAuth;
use App\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class AuthController extends Controller
{

    use RegistersUsers;
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
    	if (Auth::guard('api')->attempt($credentials)) {
		    $user = Auth::guard('api')->user();
		    $jwt = JwtAuth::generateToken($user);
		    $success = true;
		    
            return compact('success', 'user', 'jwt');
            
		} else {
			$success = false;
			$message = 'Invalid credentials';
			return compact('success', 'message');
		}
    }

    public function logout()
    {
    	Auth::guard('api')->logout();
    	$success = true;
    	return compact('success');
    }
    
    public function register(Request $request)
    {
        $this->validator($request->all());
        
        //return $request->all();

        event(new Registered($user = $this->create($request->all())));
        Auth::guard('api')->login($user);
        $jwt = JwtAuth::generateToken($user);
        $success = true;
        
        //return $request->all();
	    
	    return compact('success', 'user', 'jwt');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
   
}
