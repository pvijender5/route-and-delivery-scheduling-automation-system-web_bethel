<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class CurlService
{
    
    public function sendRequest($method, $url, $headers = [], $body = null)
    {
        $token = self::getToken(); // Now using cache instead of session

        if (!isset($headers['Authorization'])) {
            $headers['Authorization'] = $token;
        }

        if (!isset($headers['Accept'])) {
            $headers['Accept'] = 'application/json';
        }

        $aspNetSessionId = Cache::get('asp_net_session_id');
        if ($aspNetSessionId) {
            $headers['Cookie'] = "ASP.NET_SessionId={$aspNetSessionId}";
            // \Log::info('ASP.NET_SessionId added to headers', [
            //     'ASP.NET_SessionId' => $aspNetSessionId,
            //     'CookieHeader' => $headers['Cookie']
            // ]);
        }
        // \Log::info('Final headers before request', $headers);
      
        $response = null;
        try {
            if($method == "DELETE") {
                $headers['Authorization'] = $token;
                $response = Http::withToken($headers['Authorization'])
                    ->withHeader('Accept', $headers['Accept'])->delete($url, $body);
                return $response;
            } elseif ($method == "GET") {
                $headers['Authorization'] = $token;
                $response = Http::withToken($headers['Authorization'])
                    ->withHeader('Accept', $headers['Accept'])->get($url, $body);
            } elseif ($method == "POST") {
                $headers['Authorization'] = $token;
                $response = Http::withToken($headers['Authorization'])
                    ->withHeader('Accept', $headers['Accept'])
                    ->retry(3, 5000)
                    ->timeout(120)
                    ->post($url, $body);
            } elseif ($method == "PUT") {
                $headers['Authorization'] = $token;
                $response = Http::withToken($headers['Authorization'])
                    ->withHeader('Accept', $headers['Accept'])
                    ->retry(3, 5000)
                    ->timeout(120)
                    ->put($url, $body);
            } elseif ($method == "PATCH") {
                $headers['Authorization'] = $token;
                $response = Http::withToken($headers['Authorization'])
                    ->withHeader('Accept', $headers['Accept'])
                    ->patch($url, $body);
            }

            \Log::info('API Request', [
                'method' => $method,
                'url' => $url,
                'headers' => $headers,
                'body' => $body,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            return $response;
        } catch (\Exception $e) {
            \Log::error('API Request Error', [
                'method' => $method,
                'url' => $url,
                'headers' => $headers,
                'body' => $body,
                'error' => $e->getMessage(),
            ]);
        }
    }

   
    
public function getToken()
{

    $cacheKey = 'api_access_token';
    $expiresKey = 'token_expires_at';

    if (Cache::has($cacheKey) && Cache::has($expiresKey)) {
        $expiresAt = Cache::get($expiresKey);
        if ($expiresAt && now()->lt($expiresAt)) {
            return Cache::get($cacheKey);
        }else {
            \Log::info('Token expired, logging out from Acumatica before generating new token.');

            $expiredToken = Cache::get($cacheKey);
            $this->logoutFromAcumatica($expiredToken);

            // Clear expired token
            Cache::forget('api_access_token');
            Cache::forget('token_expires_at');
            Cache::forget('asp_net_session_id');
        }
    }

        return $this->generateNewToken();
}


private function generateNewToken()
{
    $url = env('API_TOKEN_URL');
    if (empty($url)) {
    \Log::error('API_TOKEN_URL is not set or empty in .env file.');
    return response()->json([
        'status' => 'error',
        'message' => 'Token URL is missing.'
    ], 500);
    }
    try {
    $response = Http::asForm()->post($url, [
        'grant_type'    => 'password',
        'client_id'     => env('API_CLIENT_ID'),
        'client_secret' => env('API_CLIENT_SECRET'),
        'username'      => env('API_USERNAME'),
        'password'      => env('API_PASSWORD'),
        'scope'         => 'api'
    ]);
     $responseHeaders = $response->headers();
        // \Log::info('Response Headers from Token Request', [
        //     'headers' => $responseHeaders
        // ]);
    if ($response->failed()) {
        \Log::error('Token Request Failed', ['response' => $response->body()]);
        return null;
    }
    
        $setCookies = $responseHeaders['Set-Cookie'] ?? [];
        $sessionId = null;
        foreach ($setCookies as $cookie) {
            if (str_starts_with($cookie, 'ASP.NET_SessionId=')) {
                $sessionId = explode(';', substr($cookie, strlen('ASP.NET_SessionId=')))[0];
                break;
            }
        }
        $data = $response->json();

    if (isset($data['access_token'])) {
        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in']; // typically in seconds
        $expiresAt = now()->addSeconds($expiresIn - 60); // subtract 60s for buffer

        Cache::put('api_access_token', $accessToken, $expiresAt);
        Cache::put('token_expires_at', $expiresAt, $expiresAt);
           if ($sessionId) {
        Cache::put('asp_net_session_id', $sessionId, $expiresAt);
        // \Log::info('Stored ASP.NET_SessionId in cache', ['ASP.NET_SessionId' => $sessionId]);
    }
    //   \Log::info('Storing new token in session', [
    //     'api_access_token' => $data['access_token'],
    //     'token_expires_at' => $expiresAt
    //   ]);

        return $data['access_token'];
    }

    \Log::error('Token Response Missing Access Token', ['response' => $data]);
    return null;
     } catch (\Exception $e) {
        \Log::error('Exception while generating token', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to generate token. Please check logs for more info.'
        ], 500);
    }
}

    private function logoutFromAcumatica($expiredToken)
    {
        if (!$expiredToken) {
            \Log::warning('No expired token available for Acumatica logout.');
            return;
        }

        $logoutUrl = env('API_BASE_URL') . '/entity/auth/logout';

        $body = [
            'access_token' => $expiredToken,
        ];

        $headers = [];

        $aspNetSessionId = Cache::get('asp_net_session_id');
        if ($aspNetSessionId) {
            $headers['Cookie'] = "ASP.NET_SessionId={$aspNetSessionId}";
            // \Log::info('ASP.NET_SessionId added to logout headers', [
            //     'ASP.NET_SessionId' => $aspNetSessionId,
            //     'CookieHeader' => $headers['Cookie']
            // ]);
        }

        // \Log::info('Sending logout request to Acumatica', [
        //     'url' => $logoutUrl,
        //     'body' => $body,
        //     'headers' => $headers
        // ]);

        try {
            // $response = Http::post($logoutUrl, $body);
            $response = Http::withHeaders($headers)->post($logoutUrl, $body);
            \Log::info('Acumatica Logout Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Acumatica Logout Request Failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }



    
}
