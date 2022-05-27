<?php declare(strict_types=1);

namespace Tests\Integration;


use Slim\Factory\AppFactory;
use Slim\Container;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Slim\Psr7\Environment;

class BaseTestSetup extends \PHPUnit\Framework\TestCase
{
    public static string $jwt = '';

    public function runApp(
        string $requestMethod,
        string $requestUri,
        string $requestParams,
    ): Response
    {
        $request = (new ServerRequestFactory())->createServerRequest($requestMethod, $requestUri . '?' . $requestParams);

        $baseDir = __DIR__ . '/../../';
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable($baseDir);
        $envFile = $baseDir . '.env';
        if (file_exists($envFile)) {
            $dotenv->load();
        }

        $basePath = __DIR__ . '/../../src/App';

        $app = AppFactory::create();

        (require $basePath . '/Middleware.php')($app);
		(require $basePath . '/Routes.php')($app);

        return $app->handle($request);
    }
}