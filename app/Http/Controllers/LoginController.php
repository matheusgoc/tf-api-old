<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    const GRANT_PASSWORD = 'password';
    const GRANT_REFRESH_TOKEN = 'refresh_token';

    /**
     * Autenticate with request credentions throw oauth
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function login(Request $request) {

        return $this->oauth(self::GRANT_PASSWORD, [
            'username' => $request->username,
            'password' => $request->password
        ]);
    }

    public function logout() {

    }

    public function refresh() {

    }

    /**
     * @param $grantType
     * @param array $data
     * @return array
     * @throws ApiException
     */
    private function oauth($grantType, array $data = []) {

        $data = array_merge($data, [
            'grant_type' => $grantType,
            'client_id' => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET')
        ]);

        try {

            $client = new Client(['base_uri' => env('APP_URL')]);
            $response = $client->post('/oauth/token', [
                'json' => $data
            ]);

        } catch (RequestException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $responseBody = json_decode((string) $response->getBody());
                throw new ApiException(
                    $responseBody->message,
                    $e->getCode(),
                    $response->getStatusCode(),
                    $responseBody->error
                );
            }

            throw new ApiException($e->getMessage());
        }

        $responseBody = json_decode((string) $response->getBody());
        return [
            'access_token' => $responseBody->access_token,
            'refresh_token' => $responseBody->refresh_token,
            'expires_in' => $responseBody->expires_in
        ];
    }
}
