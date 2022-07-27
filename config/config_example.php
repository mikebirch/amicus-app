<?php
return [
    'environment' => 'dev', // either dev or prod
    'noindex' => false, // adds tag: <meta name="robots" content="noindex" />
    'debug' => true, // true or false
    'show_debug_bar' => true, // true or false
    'database' => [
        'type' => 'mysql',
        'name' => '',
        'host' => 'mysql57',
        'username' => '',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'Cache' => [
        'expiration' => [
            'long' => 365*24*60*60, // one year
            'short' => 12*60*60 // 12 hours
        ]
    ],
    'paths' => [
        'Cache' => ROOT . DS . 'cache',
        'Logs' => ROOT . DS . 'logs',
        'Template' => APP . DS . 'Template',
        'Webroot' => ROOT . DS . 'public'
    ],    
    'blog' => [
        'url' => 'blog',
        'number_latest_posts' => 3,
        'posts_per_page' => 10,
        'show_summary_on_index' => true,
        'show_date_on_index' => false,
        'date_format_on_index' => 'j M Y',
        'date_format_on_view' => 'j F Y',
        'display_reading_times' => true,
        'display_tags_on_view' => true,
        'use_discus' => false,
        'disqus_developer_mode' => false
    ],
    'site' => [
        'url' => '',
        'email' => '',
        'noreply_email' => '',
        'files_url' => '', // url for Directus assets https://domain-name/directus-site-name/assets
        'title' => '',
        'meta_description' => '',
        'honeypot' => 'cell',
        'css_version' => 1,
        'js_version' => 1
    ],
    'rss' => [
        'title' => '',
        'description' => ''
    ],
    'google_analytics' => false
];
