<?php

namespace Wolf\HelloAsso\Sdk;

class Auth
{
    /**
     * Summary of client
     * @var Client
     */
    private $client;

    /**
     * Summary of httpClient
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.helloasso.com/',
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function getAccessToken()
    {
        $token = get_option('wolf_helloasso_access_token');
        if (!$token) {
            $token = $this->fetchAccessToken();
            if (!$token) {
                throw new \Exception('Unable to fetch access token from HelloAsso API');
            }
            add_option('wolf_helloasso_access_token', $token);
            $accessToken = json_decode($token, true)['access_token'];
        } else {
            $tokenData = json_decode($token, true);
            if (time() >= $tokenData['expires_at']) {
                $token = $this->fetchAccessToken();
                if ($token) {
                    update_option('wolf_helloasso_access_token', $token);
                }
                $accessToken = json_decode($token, true)['access_token'];
            } else {
                $accessToken = $tokenData['access_token'];
            }
        }
       return $accessToken;
    }

    private function fetchAccessToken()
    {
        $response = $this->httpClient->request('POST', 'oauth2/token', [
            'form_params' => [
                'client_id' => $this->client->getApiKey(),
                'client_secret' => $this->client->getApiSecret(),
                'grant_type' => 'client_credentials',
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            return json_encode([
                'access_token' => $data['access_token'],
                'expires_at' => time() + $data['expires_in'] - 60, // 60 seconds buffer
            ]);
        }

        return null;
    }
}