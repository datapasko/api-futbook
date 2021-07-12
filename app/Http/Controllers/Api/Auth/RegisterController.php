<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use Exception;
use Validator;
use GuzzleHttp\Client;
use App\Traits\TokenTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Laravel\Passport\Client as OClient; 

class RegisterController extends Controller
{
    
    use TokenTrait;

    public function register(Request $request)
    {        
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            'password_confirmation' => 'required|same:password', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $password = $request->password;
        $input = $request->all(); 
        $input['password'] = Hash::make($input['password']); 
        $user = User::create($input); 
        $oClient = OClient::where('password_client', 1)->first();

        $response = $this->getToken($oClient, $user->email, $password, 'password');
        
        return response()->json([
            'token' => $response->json(),
            'user' => $user
        ], $response->status());
    }

}
