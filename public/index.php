<?php declare(strict_types=1);

ini_set('display_errors', 'On');
error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$app = new \AppBase\Application();
$response = $app->handle($request);
(new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
