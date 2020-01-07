<?php

ini_set('display_errors', 'On');
require __DIR__ . '/../vendor/autoload.php';

$app = new \RestfulBooksApp\Application();

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$router = new \League\Route\Router();

// map a route
$router->map('GET', '/', function (ServerRequestInterface $request) : ResponseInterface {
    $response = new \Laminas\Diactoros\Response\JsonResponse([]);
    return $response;
});

$response = $router->dispatch($request);
(new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);


//phpinfo();
