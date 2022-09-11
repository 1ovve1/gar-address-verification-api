<?php

declare(strict_types=1);

  
use GAR\Middleware\BeforeMiddleware;

return function ($app) {
    $app->addRoutingMiddleware();


    $app->add(BeforeMiddleware::class);

    $app->addErrorMiddleware(
        filter_var($_ENV['DISPLAY_ERROR_DETAILS'], FILTER_VALIDATE_BOOLEAN),
        true,
        true,
	    (isset($_SERVER['MONOLOG'])) ? $_SERVER['MONOLOG']() : null
    );
    return $app;
};
