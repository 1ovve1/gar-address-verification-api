<?php declare(strict_types=1);

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;

return function ($app) {
  $app->add(function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type', 'application/json');
  });

  return $app;
};