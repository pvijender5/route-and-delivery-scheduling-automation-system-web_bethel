<?php

namespace App\Services;

use App\Facades\CurlFacade;

class AuthService
{
 public function authenticate()
{
    $url = "https://bethelfarms-sb.crestwoodcloud.com/identity/connect/token";
    $headers = [
        'Content-Type: application/x-www-form-urlencoded'
    ];

    $body = http_build_query([
        'grant_type'    => 'password',
        'client_id'     => env('API_CLIENT_ID'),   
        'client_secret' => env('API_CLIENT_SECRET'), 
        'username'      => env('API_USERNAME'),  
        'password'      => env('API_PASSWORD'),
        'scope'         => 'api'
    ]);

    return CurlFacade::sendRequest('POST', $url, $headers, $body);
}

}