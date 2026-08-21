<?php

namespace Wolf\HelloAsso\Sdk;

use Wolf\HelloAsso\Sdk\Model\Order;

class Checkout
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createPaymentIntent(Order $data, $organizationSlug = null)
    {
        $organizationSlug = $organizationSlug ?? $this->client->getOrganizationSlug();

        $base_args = [
            'checkout_id' => '1',
        ];

        // Build back url based on the current request if not provided
        $backUrl = str_replace('http:', 'https:', add_query_arg($base_args, home_url('/paiement-annule/')));
        $errorUrl = str_replace('http:', 'https:', add_query_arg($base_args, home_url('/paiement-erreur/')));
        $returnUrl = str_replace('http:', 'https:', add_query_arg($base_args, home_url('/paiement-reussi/')));

        $payload = [
            'totalAmount' => (int) $data->amount_cents, // en centimes
            'itemName' => $data->item_name,
            'backUrl' => $backUrl,
            'errorUrl' => $errorUrl,
            'returnUrl' => $returnUrl,
            'containsDonation' => false,
            'payer' => [
                'firstName' => $data->payer->first_name,
                'lastName' => $data->payer->last_name,
                'email' => $data->payer->email,
            ],
            'metadata' => $data->metadata
        ];


        if (empty($data->terms)) {
            $payload['initialAmount'] = (int) $data->amount_cents;
        } else {
            $terms = $data->terms;
            $payload['initialAmount'] = (int) $terms[0]['amount'] ?? 0;
            // Remove first term from the list of terms to avoid duplication
            array_shift($terms);
            $payload['terms'] = array_map(function ($term) {
                return [
                    'amount' => (int) $term['amount'] ?? 0,
                    'date' => date('Y-m-d H:i:s', $term['date']),
                ];
            }, $terms);
        }

        var_dump($payload); // Debugging line to inspect the payload

        return $this->client->request('POST', 'organizations/' . $organizationSlug . '/checkout-intents', $payload);
    }
}