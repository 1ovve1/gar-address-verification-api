<?php declare(strict_types=1);

namespace GAR\Helpers;

use Slim\Psr7\Response;

class RequestFactory
{
	static function errorResponse(string $message, int $status = 400): Response
	{
		$response = new Response();
		$response->getBody()->write(json_encode([
			'error' => $message,
		]));
		return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
	}
}