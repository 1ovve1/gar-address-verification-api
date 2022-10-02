<?php

declare(strict_types=1);

namespace GAR\Middleware;

use GAR\Exceptions\Unchecked\AddressValidationException;
use GAR\Helpers\RequestHelper;
use GAR\Helpers\ResponseCodes;
use GAR\Helpers\Validation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class AddressNameMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
		$context = RequestHelper::getRouteContextFromRequest($request);

		$region = $context->getArgument('region');
        $params = $request->getQueryParams();

		// check if set address param
        if (isset($params['address'])) {
			$userAddress = $params['address'];
        } else {
	        return RequestHelper::errorResponse("require 'address' param", ResponseCodes::PRECONDITION_FAILED_412);
        }

		// try to validate address
	    try {
		    $formattedAddress = Validation::validateUserAddress($userAddress);
	    } catch (AddressValidationException $e) {
		    return RequestHelper::errorResponse($e->getMessage(), ResponseCodes::PRECONDITION_FAILED_412);
	    }

		// given args to controller
		$response = $handler->handle(
			$request->withQueryParams([
				'address' => $formattedAddress,
				'region' => (int)$region
			])
		);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
