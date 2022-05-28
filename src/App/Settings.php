<?php declare(strict_types=1);

return [
	'settings' => [
		'displayErrorDetails' => filter_var($_ENV['DISPLAY_ERROR_DETAILS'], FILTER_VALIDATE_BOOLEAN),
        'db' => [
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'pass' => $_ENV['DB_PASS'],
            'port' => $_ENV['DB_PORT'],
        ],
        'app' => [
            'domain' => $_ENV['APP_DOMAIN'] ?? '',
            'secret' => $_ENV['SECRET_KEY'],
        ],
	]
];