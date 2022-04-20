<?php

declare(strict_types=1);

return [
	'routes' => [
		// pages
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		[
			'name' => 'page#room',
			'url' => '/rooms/{publicId}',
			'verb' => 'GET',
			'postfix' => 'shortroom'
		],
		['name' => 'page#room', 'url' => '/rooms/{publicId}/{roomName}', 'verb' => 'GET'],
		['name' => 'page#blank', 'url' => '/blank', 'verb' => 'GET'],

		// API
		['name' => 'room#index', 'url' => '/rooms', 'verb' => 'GET'],
		['name' => 'room#create', 'url' => '/rooms', 'verb' => 'POST'],
		[
			'name' => 'room#get',
			'url' => '/api/rooms/{publicId}',
			'verb' => 'GET',
		],
		['name' => 'room#delete', 'url' => '/rooms/{id}', 'verb' => 'DELETE'],
		[
			'name' => 'room#token',
			'url' => '/api/rooms/{publicId}/tokens',
			'verb' => 'POST',
		],
		['name' => 'user#get', 'url' => '/api/user', 'verb' => 'GET'],

		// assets
		['name' => 'assets#soundsTest', 'url' => '/assets/sounds/test.wav', 'verb' => 'GET'],
	],
];
