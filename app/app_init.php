<?php

use Doctrine\ORM\EntityManager;

/**
 * app_init.php must return a closure that will do init of application container, handlers, etc
 */
return function (\AppBase\Application $app) {
    $container = $app->getContainer();
    $config = $app->getConfig();

    // set modified exception handler (to process API exceptions)
    $app->setExceptionHandler(new \App\Exception\ExceptionHandler($app));

    // register an ORM
    $container->add('entity_manager', function () {
        // paths to app entities
        $paths = [
            $this->appDir() . '/Entity'
        ];

        $cache = new \Doctrine\Common\Cache\FilesystemCache($this->basePath() . '/var/cache');
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
    });

    // todo: test clean installation: vagrant, etc

    // todo: add logging, caching

    // register controllers
    $container->add(\App\Controller\ContactApiController::class)
        ->addArgument('entity_manager');
};
