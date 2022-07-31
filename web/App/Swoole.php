<?php

declare(strict_types=1);

use Imefisto\PsrSwoole\ResponseMerger;
use Imefisto\PsrSwoole\ServerRequest as PsrRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Swoole\Http\Request;
use Swoole\Http\Response;

return function ($app) {
    $http = new swoole_http_server($_ENV['SWOOLE_HOST'], intval($_ENV['SWOOLE_PORT']));
    $uriFactory = new Psr17Factory();
    $streamFactory = new Psr17Factory();
    $responseFactory = new Psr17Factory();
    $uploadedFileFactory = new Psr17Factory();
    $responseMerger = new ResponseMerger();

    $http->on('start', function (
        Swoole\Http\Server $swooleRequest
    ) {
        echo 'server started at ' . $_ENV['SWOOLE_HOST'] . ':' . $_ENV['SWOOLE_PORT'] . PHP_EOL;
    });

    $http->on(
        'request',
        function (
            Request $swooleRequest,
            Response $swooleResponse
        ) use (
            $uriFactory,
            $streamFactory,
            $uploadedFileFactory,
            $responseMerger,
            $app
        ) {
            /**
             * create psr request from swoole request
             */
            $psrRequest = new PsrRequest(
                $swooleRequest,
                $uriFactory,
                $streamFactory,
                $uploadedFileFactory
            );

            /**
             * process request (here is where slim handles the request)
             */
            $psrResponse = $app->handle($psrRequest);

            /**
             * merge your psr response with swoole response
             */
            $responseMerger->toSwoole(
                $psrResponse,
                $swooleResponse
            )->end();
        }
    );

    $http->start();
};
