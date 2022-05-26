<?php declare(strict_types=1);



return function (Slim\App $app) {
  $app->get('/address',[\GAR\Controller\AddressController::class, 'getAddressByName']);

  $app->group('/code', function(\Slim\Routing\RouteCollectorProxy $group){
    $group->get('/{type}', [\GAR\Controller\AddressController::class, 'getCodeByType']);
  });

  return $app;
};