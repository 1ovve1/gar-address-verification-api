<?php declare(strict_types=1);



return function (Slim\App $app) {
  $app->get('/address',[\GAR\Controller\AddressController::class, 'getAddressByName']);

  $app->group('/code', function(\Slim\Routing\RouteCollectorProxy $group){
    $group->get('/okato', [\GAR\Controller\AddressController::class, 'getAddressByOkato']);

    $group->get('/oktmo', [\GAR\Controller\AddressController::class, 'getAddressByOktmo']);

    $group->get('/kladr', [\GAR\Controller\AddressController::class, 'getAddressByKladr']);

  });

  return $app;
};