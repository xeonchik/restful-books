<?php declare(strict_types=1);

ini_set('display_errors', 'On');
require __DIR__ . '/../vendor/autoload.php';

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$app = new \RestfulBooksApp\Application();
$response = $app->handle($request);
(new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
