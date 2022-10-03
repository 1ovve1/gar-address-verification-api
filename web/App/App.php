<?php declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

(require __DIR__ . '/Middleware.php')($app);
(require __DIR__ . '/Routes.php')($app);
if (filter_var($_ENV['SERVER_SWOOLE_ENABLE'], FILTER_VALIDATE_BOOLEAN)) {
    (require __DIR__ . '/Swoole.php')($app);
}
