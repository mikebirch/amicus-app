<?php
return [
    'environment' => 'dev', // either dev or prod
    'debug' => true, // true or false
    'Datasources' => [
        // indices match environment
        'dev' => [
            'database_type' => 'mysql',
            'database_name' => 'name',
            'server' => 'localhost',
            'username' => 'user',
            'password' => '123456',
            'charset' => 'utf8mb4'
        ],
        'prod' => [
            'database_type' => 'mysql',
            'database_name' => 'name',
            'server' => 'localhost',
            'username' => 'user',
            'password' => '123456',
            'charset' => 'utf8mb4'
        ]
    ],
    'paths' => [
        'Cache' => ROOT . DS . 'cache',
        'Log' => ROOT . DS . 'logs',
        'Template' => APP . DS . 'Template'
    ],    
    'blog' => [
        'number_latest_posts' => 3,
        'show_summary_on_index' => true,
        'show_date_on_index' => false,
        'date_format_on_index' => 'j M Y',
        'date_format_on_view' => 'j F Y',
        'use_discus' => true,
        'disqus_developer_mode' => false
    ],
    'site' => [
        'title' => 'Michael Birch'
    ],
    'google_analytics' => true
];
