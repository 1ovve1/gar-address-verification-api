<?php declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

$baseDir = __DIR__ . '/../../';
$dotenv = Dotenv::createImmutable($baseDir);

if (file_exists($baseDir . '.env')) {
  $dotenv->load();
}
$dotenv->required([
  'DB_TYPE', 'DB_NAME', 'DB_HOST', 'DB_PORT',
  'DB_USERNAME', 'DB_PASS', 'GAR_ZIP_NAME'
]);

$app = AppFactory::create();

(require __DIR__ . '/Middleware.php')($app);
(require __DIR__ . '/Routes.php')($app);