<?php

declare(strict_types=1);

namespace GAR\Middleware;

use GAR\Exceptions\{AddressValidationException, CodeTypeNotFoundException};
use Psr\Http\Message\ResponseInterface;
use GAR\Helpers\{RequestHelper, ResponseCodes, Validation};
use GAR\Repository\Codes;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class CodeMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();

		try {
			$type = Codes::tryFindWithException($args['type']);
		} catch (CodeTypeNotFoundException $e) {
			return RequestHelper::errorResponse($e->getMessage(), ResponseCodes::PRECONDITION_FAILED_412);
		}

        $params = $request->getQueryParams();
        $formattedAddress = null;
        $objectId = null;

        if (!key_exists('address', $params)) {
            if (!key_exists('objectid', $params)) {
                return RequestHelper::errorResponse("require 'address' or 'objectid' param", ResponseCodes::PRECONDITION_FAILED_412);
            }
        }

        if (key_exists('objectid', $params)) {
            $objectId = (int)$params['objectid'];
        } else {
	        try {
		        $formattedAddress = Validation::validateUserAddress($params['address']);
	        } catch (AddressValidationException $e) {
				return RequestHelper::errorResponse($e->getMessage(), ResponseCodes::PRECONDITION_FAILED_412);
	        }
        }

		$response = $handler->handle(
			$request->withQueryParams([
				'address' => $formattedAddress,
				'objectid' => $objectId,
			])
		);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
