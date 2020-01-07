<?php

namespace RestfulBooksApp;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Application
 * Main class of our minimalistic application. That will initialize the router, controllers, configs etc...
 *
 * @package RestfulBooksApp
 * @author Maxim Tyuftin <xeonchik@gmail.com>
 */
class Application
{
    public function init()
    {
        $this->initContainer();
        $this->initDoctrine();
    }

    protected function initContainer()
    {

    }

    protected function initDoctrine()
    {

    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $this->init();

        $router = new \League\Route\Router();

        // map a route
        $router->map('GET', '/', function (ServerRequestInterface $request) : ResponseInterface {
            $response = new \Laminas\Diactoros\Response\JsonResponse([]);
            return $response;
        });

        return $router->dispatch($request);
    }
}
