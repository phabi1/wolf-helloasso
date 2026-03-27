<?php

return [
    'wolf-helloasso.history' => [
        'table' => 'wolf_helloasso_history',
        'fields' => [
            'id' => ['type' => 'integer'],
            'event_type' => ['type' => 'string'],
            'event_data' => ['type' => 'json'],
            'created_at' => ['type' => 'datetime']
        ]
    ],
    'wolf-helloasso.mapping' => [
        'table' => 'wolf_helloasso_mapping',
        'fields' => [
            'id' => ['type' => 'integer'],
            'helloasso_id' => ['type' => 'string', 'required' => true],
            'name' => ['type' => 'string', 'required' => true],
            "definition" => ['type' => 'json'],
            'created_at' => ['type' => 'datetime']
        ]
    ]
];
