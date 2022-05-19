<?php declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function($app) {
  $app->get('/test', function (Request $req, Response $resp, $args) {
    $model = GAR\Entity\EntityFactory::getAddressObjectTable();
//    var_dump($model->select(['name_addr'])->save());
    $resp->getBody()->write(json_encode($model->select(['name_addr'])->save()));
    return $resp->withHeader('Content-Type', 'application/json');
  });

  return $app;
};