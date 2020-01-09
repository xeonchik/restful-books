<?php

namespace AppBase;

use Laminas\Diactoros\Response\HtmlResponse;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ExceptionHandler
 * @package AppBase
 */
class ExceptionHandler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * ExceptionHandler constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * System default error handler
     *
     * @param \Exception $e
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(\Exception $e, ServerRequestInterface $request) : ResponseInterface
    {
        $html = '<h1>Error occurred!</h1> <p>Error: ' . $e->getMessage() . '</p>';

        $code = 500;

        if ($e instanceof NotFoundException) {
            $code = 404;
        }

        if ($this->app->isDebug()) {
            $html .= '<pre>' . $e->getTraceAsString() . '</pre>';
        }

        return new HtmlResponse($html, $code);
    }
}
