<?php

use Papryk\Imager\Application;
use Psr\Log\LoggerInterface;

require_once dirname(__DIR__) . "/vendor/autoload.php";

$container = require_once dirname(__DIR__) . "/.config/container.php";

$application = new Application($container);
$application->setLogger($container->get(LoggerInterface::class));
$application->run();
