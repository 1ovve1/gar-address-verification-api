<?php declare(strict_types=1);



return function ($app) {
  $app->get('/address',[\GAR\Controller\AddressController::class, 'getAddress']);

  return $app;
};