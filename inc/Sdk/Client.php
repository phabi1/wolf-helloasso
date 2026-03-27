<?php

namespace Wolf\HelloAsso\Sdk;

class Client
{
    private $auth;

    private $apiKey;
    private $apiSecret;

    private $organizationSlug = '';

    private $services = [];

    public function __construct($apiKey, $apiSecret, $organizationSlug = '')
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->organizationSlug = $organizationSlug;
        $this->auth = new Auth($this);
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    public function getOrganizationSlug()
    {
        return $this->organizationSlug;
    }

    public function getForms()
    {
        if (!isset($this->services['forms'])) {
            $this->services['forms'] = new Forms($this);
        }
        return $this->services['forms'];
    }

    public function request($method, $endpoint, $data = [])
    {
        $accessToken = $this->auth->getAccessToken();

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.helloasso.com/v5/',
        ]);

        $response = $client->request($method, $endpoint, [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}