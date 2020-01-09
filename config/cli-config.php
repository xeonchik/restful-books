<?php

require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$app = new \AppBase\Application();
$app->init();
return ConsoleRunner::createHelperSet($app->getContainer()->get('entity_manager'));
