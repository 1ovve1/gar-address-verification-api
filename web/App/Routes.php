<?php

declare(strict_types=1);


use GAR\Controller\AddressController;
use GAR\Middleware\AddressNameMiddleware;
use GAR\Middleware\CodeMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (Slim\App $app) {
	$app->group('/{region}', function(RouteCollectorProxy $group) {
		$group->get('/address', [AddressController::class, 'getAddressByName'])
			->add(AddressNameMiddleware::class);

		$group->group('/code', function (RouteCollectorProxy $group) {
			$group->get('/{type}', [AddressController::class, 'getCodeByType'])
				->add(CodeMiddleware::class);
		});
	});

    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile($_ENV['CACHE_PATH'] . '/route_cache.file');

    return $app;
};

