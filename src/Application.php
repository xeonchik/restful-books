<?php

namespace PhoneBook;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use League\Route\Http\Exception\NotFoundException;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use PhoneBook\Controller\ContactController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Class Application
 * Main class of our minimalistic application. That will initialize the router, controllers, configs etc...
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
     * @var EntityManager
     */
    protected $doctrine;

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

    public function __construct(string $basePath = null)
    {
        if ($basePath === null) {
            $basePath = realpath(dirname(__DIR__));
        }

        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function configDir() : string
    {
        return $this->basePath . '/config';
    }

    /**
     * Initialize application
     */
    public function init()
    {
        $this->initContainer();
        //$this->initDoctrine();
        $this->initRouter();
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

    protected function initContainer()
    {
        $container = new Container();
        $config = $this->getConfig();

        $container->add('entity_manager', function () use ($config) {
            $paths = [];

            if (isset($config['entity_paths'])) {
                foreach ($config['entity_paths'] as $path) {
                    $paths[] = $this->basePath . $path;
                }
            }

            $isDevMode = false;
            $setup = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
            $em = EntityManager::create($config['db'], $setup);
            return $em;
        });

        $container->add(ContactController::class)
            ->addArgument('entity_manager');

        $this->container = $container;
    }

    protected function initRouter()
    {
        $router = new \League\Route\Router();
        $strategy = (new ApplicationStrategy())->setContainer($this->container);
        $router->setStrategy($strategy);

        $router->group('/api', function (RouteGroup $route) {
            $route->map('GET', '/contact', 'PhoneBook\Controller\ContactController::index');
            $route->map('POST', '/contact', 'PhoneBook\Controller\ContactController::createItem');
        });

        $this->router = $router;
    }

    public function getEntityManager() : EntityManagerInterface
    {
        return $this->container->get('entity_manager');
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $this->init();

        try {
            return $this->router->dispatch($request);
        } catch (NotFoundException $exception) {
            return new JsonResponse([ 'Route for request ' . $request->getMethod() . ':' . $request->getUri()->getPath() . ' not found' ], 404);
        } catch (\Exception $exception) {
            return new JsonResponse([ 'Internal error: ' . $exception->getMessage() ], 500);
        }
    }
}
