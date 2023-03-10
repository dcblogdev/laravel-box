<?php
declare(strict_types=1);

namespace Dcblogdev\Box;

use Dcblogdev\Box\Resources\Folders;
use Dcblogdev\Box\Resources\Files;
use Dcblogdev\Box\Models\BoxToken;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;

class Box
{
    const ERROR_MARGIN = 100;

    const METHOD_GET = 'get';

    public function files(): object
    {
        return new Files();
    }

    public function folders(): object
    {
        return new Folders();
    }

    protected $baseUrl = 'https://api.box.com/2.0/';

    public function __call($function, $args): ?array
    {
        $options = ['get', 'post', 'patch', 'put', 'delete'];
        $path = (isset($args[0])) ? $args[0] : null;
        $data = (isset($args[1])) ? $args[1] : null;
        $processResponse = (isset($args[2])) ? $args[2] : false;
        $allowRedirects = (isset($args[3])) ? $args[3] : true;

        if (in_array($function, $options)) {
            return $this->guzzle($function, $path, $data, $processResponse, $allowRedirects);
        } else {
            //request verb is not in the $options array
            throw new Exception($function . ' is not a valid HTTP Verb');
        }
    }

    public function connect(): redirect
    {
        if (!request()->has('code')) {
            //redirect to box
            $url = config('box.urlAuthorize') . '?' . http_build_query([
                    'response_type' => 'code',
                    'client_id' => config('box.clientId'),
                    'redirect_uri' => config('box.redirectUri')
                ]);

            return $this->redirect($url);

        } elseif (request()->has('code')) {
            $params = [
                'grant_type' => 'authorization_code',
                'code' => request('code'),
                'client_id' => config('box.clientId'),
                'client_secret' => config('box.clientSecret')
            ];

            $token = $this->dopost(config('box.urlAccessToken'), $params);

            $expires = time() + intval($token->expires_in) - self::ERROR_MARGIN;

            $this->storeToken($token->access_token, $token->refresh_token, $expires);

            return $this->redirect(config('box.boxLandingUri'));
        }
    }

    public function getAccessToken()
    {
        $token = BoxToken::where('user_id', auth()->id())->first();

        // Check if tokens exist otherwise run the oauth request
        if (!isset($token->access_token)) {
            return $this->redirect(config('box.redirectUri'));
        }

        //process token
        return $this->getToken($token);
    }

    protected function getToken($token): string
    {
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

            $expires = time() + intval($accessToken->expires_in) - self::ERROR_MARGIN;

            // Store the new values
            $this->storeToken($accessToken->access_token, $accessToken->refresh_token, $expires);

            return $accessToken->access_token;
        }

        // Token is still valid, just return it
        return $token->access_token;
    }

    protected function storeToken($access_token, $refresh_token, $expires): void
    {
        //create a new record or if the user id exists update record
        BoxToken::updateOrCreate(['user_id' => auth()->id()], [
            'user_id' => auth()->id(),
            'access_token' => $access_token,
            'expires' => $expires,
            'refresh_token' => $refresh_token
        ]);
    }

    protected function guzzle($type, $request, $data = [], $processResponse = false, $allowRedirects = true)
    {
        try {
            $client = new Client;

            $config = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'content-type' => 'application/json',
                ],
                'allow_redirects' => $allowRedirects,
            ];

            if (!empty($data)) {
                $config['body'] = json_encode($data);
            }

            $response = $client->$type($this->baseUrl . $request, $config);

            if ($processResponse) {
                return $this->processResponse($response);
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (BadResponseException $e) {
            throw new BadResponseException($e->getMessage(), $e->getRequest(), $e->getResponse());
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

    private function processResponse(Response $response)
    {
        return [
            'reasonPhrase' => $response->getReasonPhrase(),
            'statusCode' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => $response->getBody()->getContents()
        ];
    }

    protected function redirect($url): void
    {
        header('Location: ' . $url);
        exit();
    }
}
