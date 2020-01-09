<?php

namespace App\Controller;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class IndexController
 * Default controller for index page
 *
 * @package App\Controller
 */
class IndexController
{
    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        return new HtmlResponse('<h1>Welcome to PhoneBook REST API!</h1>', 200);
    }
}
