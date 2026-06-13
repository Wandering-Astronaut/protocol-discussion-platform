<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Typesense Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('TYPESENSE_API_KEY'),
    'search_only_api_key' => env('TYPESENSE_SEARCH_ONLY_KEY'),

    'nodes' => [
        [
            'host'     => env('TYPESENSE_HOST', 'localhost'),
            'port'     => env('TYPESENSE_PORT', '8108'),
            'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
        ],
    ],

    'nearest_node' => null,

    'connection_timeout_seconds'   => 2,
    'healthy_nodes_pool_size'      => 3,
    'log_slow_requests_time'       => 5,

    /*
    |--------------------------------------------------------------------------
    | Collection Schemas
    |--------------------------------------------------------------------------
    */
    'collections' => [
        'protocols' => [
            'name'   => 'protocols',
            'fields' => [
                ['name' => 'id',           'type' => 'string'],
                ['name' => 'title',        'type' => 'string'],
                ['name' => 'content',      'type' => 'string'],
                ['name' => 'tags',         'type' => 'string[]', 'facet' => true],
                ['name' => 'author',       'type' => 'string',   'facet' => true],
                ['name' => 'avg_rating',   'type' => 'float'],
                ['name' => 'vote_score',   'type' => 'int32'],
                ['name' => 'review_count', 'type' => 'int32'],
                ['name' => 'created_at',   'type' => 'int64'],
            ],
            'default_sorting_field' => 'vote_score',
        ],

        'threads' => [
            'name'   => 'threads',
            'fields' => [
                ['name' => 'id',              'type' => 'string'],
                ['name' => 'title',           'type' => 'string'],
                ['name' => 'body',            'type' => 'string'],
                ['name' => 'tags',            'type' => 'string[]', 'facet' => true],
                ['name' => 'author',          'type' => 'string',   'facet' => true],
                ['name' => 'protocol_id',     'type' => 'string',   'facet' => true, 'optional' => true],
                ['name' => 'protocol_title',  'type' => 'string',   'optional' => true],
                ['name' => 'vote_score',      'type' => 'int32'],
                ['name' => 'comment_count',   'type' => 'int32'],
                ['name' => 'created_at',      'type' => 'int64'],
            ],
            'default_sorting_field' => 'vote_score',
        ],
    ],
];
