<?php

declare(strict_types=1);

namespace GAR\Middleware;

use GAR\Helpers\RequestHelper;
use GAR\Helpers\ResponseCodes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class BeforeMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $params = $request->getQueryParams();
        foreach ($params as $value) {
            if (!preg_match('/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ,\-. \d]*$/', $value)) {
                return RequestHelper::errorResponse("supports only rus characters, digits, '.' and ',' symbols", ResponseCodes::PRECONDITION_FAILED_412);
            }
        }

	    return $handler->handle($request);
    }

}
