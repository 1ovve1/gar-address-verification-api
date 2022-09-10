<?php

declare(strict_types=1);

namespace Tests\Integration;

defined('TEST_ENV') ?: define('TEST_ENV', __DIR__ . '/../.env.test');
require_once __DIR__ . '/../../bootstrap.php';

use GAR\Helpers\ResponseCodes;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

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

	/**
	 * @param ResponseInterface $response
	 * @param ResponseCodes $code
	 * @param array<string> $contains
	 * @param array<string> $notContains
	 * @param bool $jsonFlag
	 * @param bool $errorFlag
	 * @return void
	 */
	function assertResponse(ResponseInterface $response,
	                        ResponseCodes $code,
	                        array $contains = [],
	                        array $notContains = [],
	                        bool $jsonFlag = false,
	                        bool $errorFlag = false): void
	{
		$data = (string) $response->getBody();

		$data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
			return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
		}, $data);

		$this->assertEquals($code->value, $response->getStatusCode());
		if ($jsonFlag) {
			$this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
		}
		foreach ($contains as $contain) {
			$this->assertStringContainsString($contain, $data, "'{$data}' not contain '{$contain}'");
		}
		foreach ($notContains as $notContain) {
			$this->assertStringNotContainsString($notContain, $data, "'{$data}' contain '{$notContain}'");
		}

		if ($errorFlag) {
			$this->assertStringContainsString('error', $data);
		}
	}
}
