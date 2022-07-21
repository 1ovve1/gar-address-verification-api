<?php

declare(strict_types=1);

namespace GAR\Middleware;

use GAR\Repository\Codes;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class CodeMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();

        if (!Codes::tryFrom($args['type'])) {
            return $this->errorResponse("unavalable route for code: use 'code/all', 'code/okato', 'code/oktmo' or 'code/kladr' paths", 404);
        }

        $params = $request->getQueryParams();
        $formattedAddress = null;
        $objectId = null;

        if (!key_exists('address', $params)) {
            if (!key_exists('objectid', $params)) {
                return $this->errorResponse("require 'address' or 'objectid' param", 406);
            }
        }

        if (key_exists('objectid', $params)) {
            $objectId = (int)$params['objectid'];
        } else {
            if (strlen($params['address']) >= 1000) {
                return $this->errorResponse("address param too large", 414);
            }
        
            $formattedAddress = explode(',', $params['address']);
            foreach ($formattedAddress as $key => $value) {
                $formattedAddress[$key] = trim($value);
            }

            if (count($formattedAddress) > 1 && empty($formattedAddress[0])) {
                return $this->errorResponse("parent address shouldn't be empty", 411);
            }
        }

        $response = $handler->handle(
            $request->withQueryParams([
            'address' => $formattedAddress,
            'objectid' => $objectId
        ])
        );

        if (empty((string) $response->getBody())) {
            return $this->errorResponse("codes not found", 404);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    protected function errorResponse(string $message, int $status = 400): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
