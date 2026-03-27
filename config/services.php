<?php

return [
    'wolf-helloasso.sdk' => [
        'factory' => [\Wolf\HelloAsso\Sdk\ClientFactory::class, 'create'],
    ],
    'wolf-helloasso.webhook_bus' => [
        'class' => \Wolf\HelloAsso\WebhookBus::class,
    ],
    'wolf-helloasso.webhook.create_event' => [
        'class' => \Wolf\HelloAsso\Webhook\CreateEventWebhook::class,
        'arguments' => ['@wolf.use_case_bus', '@wolf.helper.string'],
        'tags' => [
            [                
                'name' => 'wolf-helloasso.webhook_handler',
                'value' => 'create_event',
            ]
        ]
    ],
    'wolf-helloasso.webhook.create_participants' => [
        'class' => \Wolf\HelloAsso\Webhook\CreateParticipantsWebhook::class,
        'arguments' => ['@wolf.use_case_bus'],
        'tags' => [
            [                
                'name' => 'wolf-helloasso.webhook_handler',
                'value' => 'create_participants',
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
    'wolf-helloasso.controller.mapping' => [
        'class' => \Wolf\HelloAsso\Controller\MappingController::class,
    ],
];