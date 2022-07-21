<?php

declare(strict_types=1);

namespace GAR\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class BeforeMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getQueryParams();
        foreach ($params as $value) {
            if (!preg_match('/^[A-ЯЁа-яё\,\-. \d]*$/', $value)) {
                return $this->errorResponse("supports only rus characters, digits, '.' and ',' symbols", 415);
            }
        }

        $response = $handler->handle($request);

        return $response;
    }


    protected function errorResponse(string $message, int $status = 400): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
