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
    'wolf-helloasso.controller.mapping' => [
        'class' => \Wolf\HelloAsso\Controller\MappingController::class,
    ],
    'wolf-helloasso.webhook_bus' => [
        'class' => \Wolf\HelloAsso\Webhook\WebhookBus::class,
    ],
    'wolf-helloasso.webhook.handler.receive_payment' => [
        'class' => \Wolf\HelloAsso\Webhook\Handler\ReceivePaymentHandler::class,
        'arguments' => ['@wolf.use_case_bus', '@wolf.helper.string'],
        'tags' => [
            [
                'name' => 'wolf-helloasso.webhook_handler',
                'value' => 'receive_payment',
            ]
        ]
    ],
    'wolf-helloasso.use_case.get_mapping' => [
        'class' => \Wolf\HelloAsso\UseCase\GetMappingUseCase::class,
        'arguments' => ['@wolf.entity.manager'],
        'tags' => [
            [
                'name' => 'use_case',
                'value' => 'wolf-helloasso.get_mapping'
            ]
        ]
    ],
    'wolf-helloasso.use_case.create_mapping' => [
        'class' => \Wolf\HelloAsso\UseCase\CreateMappingUseCase::class,
        'arguments' => ['@wolf.entity.manager'],
        'tags' => [
            [
                'name' => 'use_case',
                'value' => 'wolf-helloasso.create_mapping'
            ]
        ]
    ],
    'wolf-helloasso.use_case.synchronize_event' => [
        'class' => \Wolf\HelloAsso\UseCase\SynchronizeEventUseCase::class,
        'arguments' => ['@wolf.use_case_bus', '@wolf.entity.manager', '@wolf-helloasso.sdk', '@wolf.helper.string'],
        'tags' => [
            [
                'name' => 'use_case',
                'value' => 'wolf-helloasso.synchronize_event'
            ]
        ]
    ],
];