<?php

declare(strict_types=1);

namespace GAR\Middleware;

use GAR\Exceptions\AddressValidationException;
use GAR\Helpers\ResponseCodes;
use GAR\Helpers\Validation;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use GAR\Helpers\RequestHelper;

class AddressNameMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): \Psr\Http\Message\ResponseInterface
    {
        $params = $request->getQueryParams();
        
        if (isset($params['address'])) {
			$userAddress = $params['address'];
        } else {
	        return RequestHelper::errorResponse("require 'address' param", ResponseCodes::PRECONDITION_FAILED_412);
        }

	    try {
		    $formattedAddress = Validation::validateUserAddress($userAddress);
	    } catch (AddressValidationException $e) {
		    return RequestHelper::errorResponse($e->getMessage(), ResponseCodes::PRECONDITION_FAILED_412);
	    }

		$response = $handler->handle(
			$request->withQueryParams([
				'address' => $formattedAddress,
			])
		);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
