<?php declare(strict_types=1);

  
use GAR\Logger\Log;

return function ($app) {

  $app->addRoutingMiddleware();


  $app->add(\GAR\Middleware\BeforeMiddleware::class);

  $app->addErrorMiddleware(
    filter_var($_ENV['DISPLAY_ERROR_DETAILS'], FILTER_VALIDATE_BOOLEAN), 
    true, 
    true, 
    Log::getInstance()
  );
  return $app;
};