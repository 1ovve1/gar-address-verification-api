<?php

declare(strict_types=1);

namespace Tests\Integration;

require __DIR__ . '/../../bootstrap.php';

use Slim\Container;
use Slim\Factory\AppFactory;
use Slim\Psr7\Environment;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class BaseTestSetup extends \PHPUnit\Framework\TestCase
{
    public static string $jwt = '';

    public function runApp(
        string $requestMethod,
        string $requestUri,
        string $requestParams,
    ): Response {
        $request = (new ServerRequestFactory())->createServerRequest($requestMethod, $requestUri . '?' . $requestParams);

        $basePath = __DIR__ . '/../../web/App';

        $app = AppFactory::create();

        (require $basePath . '/Middleware.php')($app);
        (require $basePath . '/Routes.php')($app);

        return $app->handle($request);
    }
}
