<?php

require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$app = new \PhoneBook\Application();
$app->init();
return ConsoleRunner::createHelperSet($app->getEntityManager());
