<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

return [

    /* Activate tracy
    |--------------------------------------------------------------------------
    | Available values:
    | true      – Enable for any context
    | false     – Disable for any context
    | 'manager' – Enable only for manager context (admin area)
    | 'web'     – Enable only for web context (public area)
    |-------------------------------------------------------------------------- */

    'enabled' => env('TRACY_ENABLED', env('APP_DEBUG') === true),

    /* Show bar
    |-------------------------------------------------------------------------- */

    'showBar' => env('TRACY_SHOW_BAR', env('APP_ENV') !== 'production'),

    /* Show exceptions
    |-------------------------------------------------------------------------- */

    'showException' => env('TRACY_EXCEPTION', true),

    /* The URL prefix for the manager dashboard
    |-------------------------------------------------------------------------- */

    'managerPrefix' => 'admin',

    /* The URL prefix for a frame top level the manager dashboard
    |-------------------------------------------------------------------------- */

    'managerTopRoute' => 'main',

    /* If true tracy shown bar in a frame top level
    | instead pages frames in the manager context
    |-------------------------------------------------------------------------- */

    'enabledInTopFrame' => env('TRACY_MGR_TOP_FRAME', false),

    'route'         => [
        'prefix' => 'tracy',
        'as'     => 'tracy.',
    ],
    'accepts'       => [
        'text/html',
    ],
    'appendTo'      => 'body',
    'editor'        => 'editor://%action/?file=%file&line=%line&search=%search&replace=%replace',
    'maxDepth'      => 4,
    'maxLength'     => 1000,
    'scream'        => true,
    'showLocation'  => true,
    'strictMode'    => true,
    'editorMapping' => [],
    'panels'        => [
        'routing'        => true,
        'database'       => true,
        'view'           => true,
        'event'          => false,
        'session'        => true,
        'request'        => true,
        'auth'           => true,
        'html-validator' => false,
    ],
];
