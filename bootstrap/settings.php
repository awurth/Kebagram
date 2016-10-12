<?php
return [
    'settings' => [

    	// Slim genereal settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,

        // View settings (template engine Twig)
        'view' => [
            'template_path' => __DIR__ . '/../resources/views',
            'twig' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // Database
        'db' => parse_ini_file(__DIR__ . '/db.config.ini')
    ],
];