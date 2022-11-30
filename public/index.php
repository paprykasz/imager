<?php

use Papryk\Imager\Application;
use Papryk\Imager\Container;
use Psr\Log\LoggerInterface;

require_once dirname(__DIR__) . "/vendor/autoload.php";

$container = Container::build();

$application = new Application($container);
$application->setLogger($container->get(LoggerInterface::class));
$application->run();
