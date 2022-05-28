<?php declare(strict_types=1);

namespace GAR\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Psr7\Response;

class AddressNameMiddleware {
	function __invoke(Request $request, RequestHandler $handler) : Response
	{
		$params = $request->getQueryParams();
		
		if (!key_exists('address', $params)) {
			return $this->errorResponse("require 'address' param", 406);
		} else if (strlen($params['address']) >= 1000) {
			return $this->errorResponse("address param too large", 414);
		}

		$formattedAddress = explode(',', $params['address']);
		foreach ($formattedAddress as $key => $value) {
			$formattedAddress[$key] = trim($value);
		}

		if (count($formattedAddress) > 1 && empty($formattedAddress[0])) {
			return $this->errorResponse("parent address shouldn't be empty", 411);
		}

    	$response = $handler->handle(
    		$request->withQueryParams([
    			'address' => $formattedAddress
    		])
    	);

    	if (empty((string)$response->getBody())) {
    		return $this->errorResponse("address not found", 404);	
    	}

    	return $response->withHeader('Content-Type', 'application/json');
  	}


	protected function errorResponse(string $message, int $status = 400) : Response
  	{
  		$response = new Response();
    	$response->getBody()->write(json_encode(['error' => $message]));
	    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
	  }
}