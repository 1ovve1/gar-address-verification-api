<?php declare(strict_types=1);

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use GAR\Logger\Log;

return function ($app) {

  $app->addRoutingMiddleware();


  $app->add(function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type', 'application/json');
  });

  $app->addErrorMiddleware(
    filter_var($_ENV['DISPLAY_ERROR_DETAILS'], FILTER_VALIDATE_BOOLEAN), 
    true, 
    true, 
    Log::getInstance()
  );
  return $app;
};