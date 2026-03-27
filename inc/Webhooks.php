<?php

namespace Wolf\HelloAsso;

use Wolf\Core\Plugin;

class Webhooks
{
    public function setup()
    {
        add_action('rest_api_init', function () {
            register_rest_route('wolf-helloasso/v1', '/webhooks', [
                'methods' => 'POST',
                'callback' => [$this, 'handleHelloAssoWebhook'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    public function handleHelloAssoWebhook(\WP_REST_Request $request)
    {
        //Check valid ip
        $valid_ips = ['51.138.206.200']; // Replace with actual IP addresses
        $request_ip = $_SERVER['REMOTE_ADDR'];
        if (!in_array($request_ip, $valid_ips)) {
            // return new \WP_REST_Response(['status' => 'forbidden'], 403);
        }

        $payload = $request->get_json_params();

        if (empty($payload['eventType'])) {
            return new \WP_REST_Response(['status' => 'bad_request', 'message' => 'Missing event field'], 400);
        }

        $container = Plugin::getContainer();

        $queryParams = $request->get_query_params();

        if (!isset($queryParams['log']) || $queryParams['log'] !== 'false') {
            $webhookRepository = $container->get('wolf.entity.manager')->getRepository('wolf-helloasso.history');
            $webhookRepository->insert([
                'event_type' => $payload['eventType'],
                'event_data' => $payload['data'] ?? [],
                'created_at' => time(),
            ]);
        }
        
        $eventName = $this->mapEventToEventName($payload);

        if ($eventName) {

            $webhookBus = $container->get('wolf-helloasso.webhook_bus');
            $webhookBus->execute($eventName, $payload['data'] ?? []);
        }

        return new \WP_REST_Response(['status' => 'success'], 200);
    }

    private function mapEventToEventName(array $payload): string
    {
        // Map the eventType from the payload to the corresponding event name
        $eventType = $payload['eventType'] ?? '';

        switch ($eventType) {
            case 'Form':
                return 'create_event';
            case 'Order':
                return 'create_participants';
            // Add more cases as needed for different event types
            default:
                return null; // Fallback to the original event type if no mapping is found
        }
    }
}