<?php declare(strict_types=1);

use DB\ORM\Resolver\AST;

$default_driver = require 'default_driver.php';

// change ILIKE condition cause in mysql it is not supported
$default_driver[AST::COND][AST::COND_ILIKE] = 'LIKE';

return $default_driver;