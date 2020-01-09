<?php

namespace App\Exception;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExceptionHandler extends \AppBase\ExceptionHandler
{
    public function handle(\Exception $exception, ServerRequestInterface $request): ResponseInterface
    {
        $code = 500; // default code

        if ($exception instanceof NotFoundException) {
            return new JsonResponse([
                'success' => false,
                'error' => $exception->getMessage()
            ], $code);
        }

        return parent::handle($exception, $request);
    }
}
