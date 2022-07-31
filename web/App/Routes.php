<?php

declare(strict_types=1);



return function (Slim\App $app) {
    $app->get('/address', [\GAR\Controller\AddressController::class, 'getAddressByName'])
    ->add(\GAR\Middleware\AddressNameMiddleware::class);

    $app->group('/code', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('/{type}', [\GAR\Controller\AddressController::class, 'getCodeByType'])
      ->add(\GAR\Middleware\CodeMiddleware::class);
    });

    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile($_ENV['CACHE_PATH'] . '/route_cache.file');

    return $app;
};
