<?php

declare(strict_types=1);

namespace GAR\Middleware;

use GAR\Exceptions\{Unchecked\AddressValidationException, Unchecked\CodeTypeNotFoundException};
use GAR\Helpers\{RequestHelper, ResponseCodes, Validation};
use GAR\Storage\Codes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CodeMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
		$context = RequestHelper::getRouteContextFromRequest($request);

		$typeStr = $context->getArgument('type');
		$region = $context->getArgument('region');
        $params = $request->getQueryParams();
        $formattedAddress = null;
        $objectId = null;

		// try to find code from enum-contract
	    try {
		    $type = Codes::tryFindWithException($typeStr);
	    } catch (CodeTypeNotFoundException $e) {
		    return RequestHelper::errorResponse($e->getMessage(), ResponseCodes::PRECONDITION_FAILED_412);
	    }

		// check if params given
        if (!key_exists('address', $params)) {
            if (!key_exists('objectid', $params)) {
                return RequestHelper::errorResponse("require 'address' or 'objectid' param", ResponseCodes::PRECONDITION_FAILED_412);
            }
        }

		// if given objectid - we take it
        if (key_exists('objectid', $params)) {
            $objectId = (int)$params['objectid'];
        } else {
			// if given address - we validate address
	        try {
		        $formattedAddress = Validation::validateUserAddress($params['address']);
	        } catch (AddressValidationException $e) {
				return RequestHelper::errorResponse($e->getMessage(), ResponseCodes::PRECONDITION_FAILED_412);
	        }
        }

		// given args to controller
		$response = $handler->handle(
			$request->withQueryParams([
				'address' => $formattedAddress,
				'objectid' => $objectId,
				'type' => $type,
				'region' => (int)$region
			])
		);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
