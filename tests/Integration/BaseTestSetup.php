<?php

declare(strict_types=1);

namespace Tests\Integration;

define('TEST_ENV', __DIR__ . '/../.env.test');

require_once __DIR__ . '/../../bootstrap.php';

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class BaseTestSetup extends \PHPUnit\Framework\TestCase
{
    public static string $jwt = '';

    public function runApp(
        string $requestMethod,
        string $requestUri,
        string $requestParams,
    ): \Psr\Http\Message\ResponseInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest($requestMethod, $requestUri . '?' . $requestParams);

        $basePath = __DIR__ . '/../../web/App';

        $app = AppFactory::create();

        (require $basePath . '/Middleware.php')($app);
        (require $basePath . '/Routes.php')($app);

        return $app->handle($request);
    }
}
