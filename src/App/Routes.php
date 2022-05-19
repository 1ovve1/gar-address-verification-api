<?php declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function($app) {
  $app->get('/test', function (Request $req, Response $resp, $args) {
    $resp->getBody()->write('Hello World');
    return $resp;
  });

  return $app;
};