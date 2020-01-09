<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * app_init.php must return a closure that will do init of application container, handlers, etc
 */
return function (\AppBase\Application $app) {
    $container = $app->getContainer();
    $config = $app->getConfig();

    // set modified exception handler (to process API exceptions)
    $app->setExceptionHandler(new \App\Exception\ExceptionHandler($app));

    // register an ORM
    $container->add('entity_manager', function () use ($config, $app) {
        $paths = [
            $app->appDir() . '/Entity'
        ];

        // TODO: add cache to doctrine

        $setup = Setup::createAnnotationMetadataConfiguration($paths, $app->isDebug());
        $em = EntityManager::create($config['db'], $setup);
        return $em;
    });

    // register controllers
    $container->add(\App\Controller\ContactApiController::class)
        ->addArgument('entity_manager');
};
