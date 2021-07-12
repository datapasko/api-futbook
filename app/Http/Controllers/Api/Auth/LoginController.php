<?php

namespace App\Http\Controllers\Api\Auth;

use Validator;
use App\Traits\TokenTrait;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    use TokenTrait;
    
    private $client;

    public function __construct(){
        $this->client = Client::find(1);
    }

    public function login (Request $request){

        $validator = Validator::make($request->all(), [ 
            'email' => 'required', 
            'password' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $response = $this->getToken($this->client, $request->email, $request->password, 'password');
        
        $initUser = Auth::attempt(['email'=>$request->email, 'password'=>$request->password]);        
        $user = $initUser ? Auth::user() : null;

        return response()->json([
            'token' => $response->json(),
            'user' => $user
        ], $response->status());
    }

    public function refresh (Request $request){

        $validator = Validator::make($request->all(), [ 
            'refresh_token' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        return $this->refreshToken($this->client, $request->refresh_token);
    }

    public function logout (Request $request){

    	$accessToken = Auth::user()->token();

        $this->revokeToken($accessToken->id);

    	return response()->json([], 204);
    }

    public function test (Request $request){
    	return true;
    }
}
