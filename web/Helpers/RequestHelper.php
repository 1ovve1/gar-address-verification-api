<?php declare(strict_types=1);

namespace GAR\Helpers;

use GAR\Exceptions\Unchecked\ServerSideProblemException;
use http\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class RequestHelper
{
	/**
	 * @return Response
	 */
	static function createEmptyResponse(): Response
	{
		return new Response();
	}

	/**
	 * @param string $uri
	 * @param array<string> $params
	 * @param string $method
	 * @return ServerRequestInterface
	 */
	static function createRequest(string $uri, array $params, string $method = 'GET'): ServerRequestInterface
	{
		$paramsCollection = [];
		foreach ($params as $name => $value) {

			$paramsCollection[] = $name . '=' . $value;
		}

		$uriWithParams = match (empty($paramsCollection)) {
			true => $uri,
			false => $uri . '?' . implode('&', $paramsCollection)
		};

		return (new ServerRequestFactory())->createServerRequest($method, $uriWithParams);
	}

	/**
	 * @param string $message
	 * @param ResponseCodes $status
	 * @param bool $jsonType
	 * @return Response
	 */
	static function errorResponse(string $message, ResponseCodes $status, bool $jsonType = true): Response
	{
		$response = new Response();
		self::writeDataJson($response, ['error' => $message]);

		$statusValue = $status->value;

		return match($jsonType) {
			true => $response->withHeader('Content-Type', 'application/json')->withStatus($statusValue),
			false => $response->withStatus($statusValue),
		};
	}

	/**
	 * @param ResponseInterface &$response
	 * @param array<mixed> $data
	 * @param int $flag
	 * @return void
	 */
	static function writeDataJson(ResponseInterface $response, array $data, int $flag = JSON_FORCE_OBJECT): void
	{
		if ($data = json_encode($data, JSON_FORCE_OBJECT)) {
			$response->getBody()->write($data);
		} else {
			throw new RuntimeException('Cannot convert data to JSON format');
		}
	}
}