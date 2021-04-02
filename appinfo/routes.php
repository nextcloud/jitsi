<?php

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\jitsi\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */

return [
    'routes' => [
        // pages
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        [
            'name'    => 'page#room',
            'url'     => '/rooms/{publicId}',
            'verb'    => 'GET',
            'postfix' => 'shortroom'
        ],
        ['name' => 'page#room', 'url' => '/rooms/{publicId}/{roomName}', 'verb' => 'GET'],

        // API
        ['name' => 'room#index', 'url' => '/rooms', 'verb' => 'GET'],
        ['name' => 'room#create', 'url' => '/rooms', 'verb' => 'POST'],
        [
            'name' => 'room#get',
            'url'  => '/api/rooms/{publicId}',
            'verb' => 'GET',
        ],
        ['name' => 'room#delete', 'url' => '/rooms/{id}', 'verb' => 'DELETE'],
        [
            'name' => 'room#token',
            'url'  => '/api/rooms/{publicId}/tokens',
            'verb' => 'POST',
        ],
        ['name' => 'user#get', 'url' => '/api/user', 'verb' => 'GET'],

        // assets
        ['name' => 'assets#soundsTest', 'url' => '/assets/sounds/test', 'verb' => 'GET'],
    ],
];
