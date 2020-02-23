<?php

namespace Dcblogdev\Box;

/**
* box api documenation can be found at https://developer.box.com/reference
**/

use Dcblogdev\Box\Resources\Folders;
use Dcblogdev\Box\Resources\Files;
use Dcblogdev\Box\Models\BoxToken;
use GuzzleHttp\Client;
use Exception;

class Box
{
    public function files() {
        return new Files();
    }

    public function folders() {
        return new Folders();
    }

    protected static $baseUrl = 'https://api.box.com/2.0/';

    public function __call($function, $args)
    {
        $options = ['get', 'post', 'patch', 'put', 'delete'];
        $path = (isset($args[0])) ? $args[0] : null;
        $data = (isset($args[1])) ? $args[1] : null;

        if (in_array($function, $options)) {
            return self::guzzle($function, $path, $data);
        } else {
            //request verb is not in the $options array
            throw new Exception($function.' is not a valid HTTP Verb');
        }
    }

    public function connect()
    {
        if (!request()->has('code')) {

            //redirect to box
            $url = config('box.urlAuthorize') . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => config('box.clientId'),
                'redirect_uri' => config('box.redirectUri')
            ]);

            return redirect()->away($url);

        } elseif (request()->has('code')) {

            $params = [
                'grant_type' => 'authorization_code',
                'code' => request('code'),
                'client_id' => config('box.clientId'),
                'client_secret' => config('box.clientSecret')
            ];

            $token = $this->dopost(config('box.urlAccessToken'), $params);

            $this->storeToken($token->access_token, $token->refresh_token, $token->expires_in);

            return redirect(config('box.boxLandingUri'));
        }
    }

    public function getAccessToken()
    {
        $token = BoxToken::where('user_id', auth()->id())->first();

        // Check if tokens exist otherwise run the oauth request
        if (!isset($token->access_token)) {
            return redirect(config('box.redirectUri'));
        }

        // Check if token is expired
        // Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if ($token->expires <= $now) {
            // Token is expired (or very close to it) so let's refresh
		    $params = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
                'client_id' => config('box.clientId'),
                'client_secret' => config('box.clientSecret')
            ];
            $accessToken = $this->dopost(config('box.urlAccessToken'), $params);

            // Store the new values
            $this->storeToken($accessToken->access_token, $accessToken->refresh_token, $accessToken->expires_in);

            return $accessToken->access_token;
        } else {
            // Token is still valid, just return it
            return $token->access_token;
        }
    }

    protected function storeToken($access_token, $refresh_token, $expires)
    {
        //cretate a new record or if the user id exists update record
        BoxToken::updateOrCreate(['user_id' => auth()->id()], [
            'user_id'       => auth()->id(),
            'access_token'  => $access_token,
            'expires'       => $expires,
            'refresh_token' => $refresh_token
        ]);
    }

    protected function guzzle($type, $request, $data = [])
    {
        try {
            $client = new Client;

            $response = $client->$type(self::$baseUrl.$request, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->getAccessToken(),
                    'content-type' => 'application/json',
                ],
                'body' => json_encode($data),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected static function dopost($url, $params)
    {
        try {
            $client = new Client;
            $response = $client->post($url, ['form_params' => $params]);

            return json_decode($response->getBody()->getContents());

        } catch (Exception $e) {
            return json_decode($e->getResponse()->getBody()->getContents(), true);
        }
	}
}
