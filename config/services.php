<?php

return [
    'wolf-helloasso.sdk' => [
        'factory' => [\Wolf\HelloAsso\Sdk\ClientFactory::class, 'create'],
    ],
    'wolf-helloasso.payment.strategy.helloasso' => [
        'class' => \Wolf\HelloAsso\Payment\Strategy\HelloAsso::class,
        'arguments' => ['@wolf-helloasso.sdk'],
        'tags' => [
            ['name' => 'wolf_billing.payment.strategy', 'value' => 'helloasso']
        ]
    ],
    'wolf-helloasso.payment.strategy.multiplehelloasso' => [
        'class' => \Wolf\HelloAsso\Payment\Strategy\MultipleHelloAsso::class,
        'arguments' => ['@wolf-helloasso.sdk'],
        'tags' => [
            ['name' => 'wolf_billing.payment.strategy', 'value' => 'multiplehelloasso']
        ]
    ],
    'wolf-helloasso.controller.webhook' => [
        'class' => \Wolf\HelloAsso\Controller\WebhookController::class,
    ],
    'wolf-helloasso.webhook_bus' => [
        'class' => \Wolf\HelloAsso\Webhook\WebhookBus::class,
    ],
    'wolf-helloasso.webhook.handler.receive_order' => [
        'class' => \Wolf\HelloAsso\Webhook\Handler\ReceiveOrderHandler::class,
        'arguments' => ['@wolf.helper.string'],
        'tags' => [
            [
                'name' => 'wolf-helloasso.webhook_handler',
                'value' => 'receive_order',
            ]
        ]
    ],
    'wolf-helloasso.webhook.handler.receive_payment' => [
        'class' => \Wolf\HelloAsso\Webhook\Handler\ReceivePaymentHandler::class,
        'arguments' => ['@wolf.use_case_bus'],
        'tags' => [
            [
                'name' => 'wolf-helloasso.webhook_handler',
                'value' => 'receive_payment',
            ]
        ]
    ]
];