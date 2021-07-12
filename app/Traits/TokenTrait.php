<?php
namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Client as OClient; 

trait TokenTrait {
    
    public $successStatus = 200;

    public function getToken(OClient $oClient, $username, $password, $granType) { 

        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [            
            'grant_type' => $granType,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $username,
            'password' => $password,
            'scope' => '*',
        ]);

        return $response;   
    }

    public function refreshToken(OClient $oClient, $token) { 

        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [            
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
        ]);

        return response()->json($response->json(), $response->status());   
    }

    public function revokeToken($tokenId){
        $tokenRepository = app(TokenRepository::class);

        // Revoke an access token...
        $tokenRepository->revokeAccessToken($tokenId);
    }
}