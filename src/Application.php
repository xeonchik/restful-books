<?php

namespace AppBase;

use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Application
 * Base class of application. That will initialize the router, controllers, configs etc...
 *
 * @package PhoneBook
 * @author Maxim Tyuftin <xeonchik@gmail.com>
 */
class Application
{
    /**
     * Root path of this app
     * @var string
     */
    protected $basePath;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var null|array
     */
    protected $config = null;

    /**
     * @var string
     */
    protected string $mode = 'production';

    /**
     * @var ExceptionHandler
     */
    protected $exceptionHandler;

    public function __construct(string $basePath = null)
    {
        if ($basePath === null) {
            $basePath = realpath(dirname(__DIR__));
        }

        $this->basePath = $basePath;
        $this->init();
    }

    /**
     * @return string
     */
    public function configDir() : string
    {
        return $this->basePath . '/config';
    }

    /**
     * @return string
     */
    public function appDir() : string
    {
        return $this->basePath . '/app';
    }

    /**
     * Initialize application
     */
    public function init()
    {
        $this->initContainer();
        $this->initRouter();

        $this->mode = $this->getConfig()['mode'] ?? 'production';

        // init default error handler
        $this->exceptionHandler = new ExceptionHandler($this);

        // init app, containers
        if (file_exists($this->appDir() . '/app_init.php')) {
            $initFunction = include $this->appDir() . '/app_init.php';
            if (is_callable($initFunction)) {
                $initFunction($this);
            }
        }
    }

    /**
     * Initializing of container for DI
     *
     * @throws \Exception
     */
    protected function initContainer()
    {
        $container = new Container();
        $this->container = $container;
    }

    /**
     * Initializing of app routes
     */
    protected function initRouter()
    {
        $router = new Router();
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->container);
        $router->setStrategy($strategy);
        $this->router = $router;

        // attach routes from app
        if (file_exists($this->appDir() . '/app_routes.php')) {
            $routesFunction = include $this->appDir() . '/app_routes.php';
            if (is_callable($routesFunction)) {
                $routesFunction($this);
            }
        }
    }

    /**
     * Reads the app config (simple array)
     * @return array
     * @throws \Exception
     */
    public function getConfig() : array
    {
        if (is_array($this->config)) {
            return $this->config;
        }

        $configPath = $this->configDir() . '/config.php';

        if (!file_exists($configPath)) {
            throw new \Exception("Config ($configPath) does not exists");
        }

        $data = include $configPath;

        if (!is_array($data)) {
            throw new \Exception('Config must be an array');
        }

        $this->config = $data;
        return $data;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @return ExceptionHandler
     */
    public function getExceptionHandler(): ExceptionHandler
    {
        return $this->exceptionHandler;
    }

    /**
     * @param ExceptionHandler $exceptionHandler
     */
    public function setExceptionHandler(ExceptionHandler $exceptionHandler): void
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * Start point of app
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            return $this->router->dispatch($request);
        } catch (\Exception $e) {
            return $this->exceptionHandler->handle($e, $request);
        }
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->mode == 'dev' ? true : false;
    }
}
