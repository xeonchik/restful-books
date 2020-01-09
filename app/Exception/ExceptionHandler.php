<?php

namespace App\Exception;

use AppBase\Application;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ExceptionHandler
 * This handler process exceptions and prepare JSON response
 * @package App\Exception
 */
class ExceptionHandler extends \AppBase\Exception\ExceptionHandler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ExceptionHandler constructor.
     * @param Application $application
     * @param LoggerInterface $logger
     */
    public function __construct(Application $application, LoggerInterface $logger)
    {
        parent::__construct($application);
        $this->logger = $logger;
    }

    /**
     * @param \Exception $exception
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(\Exception $exception, ServerRequestInterface $request): ResponseInterface
    {
        $code = 500; // default code

        if ($exception instanceof NotFoundException) {
            $code = 404;
        }

        // Logging of errors
        if ($code >= 500) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception,
                'uri' => $request->getUri()->__toString()
            ]);
        } else if ($code >= 400) {
            $this->logger->warning($exception->getMessage(), [
                'uri' => $request->getUri()->__toString()
            ]);
        }

        if ($exception instanceof \League\Route\Http\Exception) {
            $code = (int)$exception->getStatusCode();
        }

        return new JsonResponse([
            'success' => false,
            'error' => $exception->getMessage()
        ], $code);
    }
}
