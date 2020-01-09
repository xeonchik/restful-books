<?php

use Doctrine\ORM\EntityManager;

/**
 * app_init.php must return a closure that will do init of application container, handlers, etc
 */
return function (\AppBase\Application $app) {
    $container = $app->getContainer();

    // register an ORM
    $container->add('entity_manager', function ($cache) {
        // paths to app entities
        $paths = [
            $this->appDir() . '/Entity'
        ];

        $configuration = new \Doctrine\ORM\Configuration();
        $configuration->setMetadataDriverImpl($configuration->newDefaultAnnotationDriver($paths));
        $configuration->setMetadataCacheImpl($cache);
        $configuration->setQueryCacheImpl($cache);
        $configuration->setProxyDir($this->basePath() . '/var/proxies');
        $configuration->setProxyNamespace('App\Proxies');

        if ($this->isDebug()) {
            $configuration->setAutoGenerateProxyClasses(true);
        } else {
            $configuration->setAutoGenerateProxyClasses(false);
        }

        $em = EntityManager::create($this->getConfig()['db'], $configuration);
        return $em;
    })->addArgument('cache');

    // external APIs service
    $container->add(\App\Service\ReferenceService::class)
        ->addArgument(\AppBase\Cache\CacheWrapper::class);

    // todo: test clean installation: vagrant, etc

    // cache factory
    $container->add('cache', function () {
        $config = $this->getConfig()['cache'] ?? null;

        // use filesystem cache by default
        if (!$config) {
            return new \Doctrine\Common\Cache\FilesystemCache($this->basePath() . '/var/cache');
        }

        $redis = new Redis();
        $redis->connect($config['host'], $config['port']);

        $cache = new \Doctrine\Common\Cache\RedisCache();
        $cache->setRedis($redis);
        return $cache;
    });

    // cache wrapper service
    $container->add(\AppBase\Cache\CacheWrapper::class)
        ->addArgument('cache');

    // register controllers
    $container->add(\App\Controller\ContactApiController::class)
        ->addArgument('entity_manager')
        ->addArgument(\App\Service\ReferenceService::class);

    // default logger
    $container->add('logger', function () {
        $logger = new \Monolog\Logger('app');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($this->basePath() . '/var/logs/error.log', \Monolog\Logger::WARNING));
        return $logger;
    });

    // set modified exception handler (to process API exceptions)
    $app->setExceptionHandler(new \App\Exception\ExceptionHandler($app, $container->get('logger')));
};
