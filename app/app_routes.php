<?php

use League\Route\RouteGroup;

/**
 * app_router.php must return a closure that will initialize the app routes
 */
return function (\AppBase\Application $app) {
    $router = $app->getRouter();

    /**
     * This routers for RESTful API of phone book
     */
    $router->group('/api', function (RouteGroup $route) {
        $route->map('GET', '/contact/{id}', 'App\Controller\ContactApiController::getItem');
        $route->map('DELETE', '/contact/{id}', 'App\Controller\ContactApiController::deleteItem');
        $route->map('GET', '/contact-list', 'App\Controller\ContactApiController::getList');
        $route->map('POST', '/contact', 'App\Controller\ContactApiController::createItem');
    });

    $router->map('GET', '/', 'App\Controller\IndexController::index');
};
