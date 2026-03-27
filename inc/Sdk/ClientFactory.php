<?php

namespace Wolf\HelloAsso\Sdk;

class ClientFactory {
    public static function create()
    {
        $options = get_option('wolf_helloasso_credentials', []);
        $organizationSlug = get_option('wolf_helloasso_organization_slug', '');
        $apiKey = $options['api_key'] ?? '';
        $apiSecret = $options['api_secret'] ?? '';
        return new Client($apiKey, $apiSecret, $organizationSlug);
    }
}