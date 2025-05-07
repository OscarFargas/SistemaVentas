<?php
    #phpinfo();
    #llamar metodo estatico ::
    # llamar metodo ->

    use Slim\Factory\AppFactory;
    
    use DI\Container;
    // recarga todas las dependencias isntaladas automaticamente
    require __DIR__ . '/../../vendor/autoload.php';
    
    $container = new Container();
    AppFactory::setContainer($container);
    
    $app = AppFactory::create();
    
    require 'config.php';
    require 'conexion.php';
    require 'routes.php';
    
    $app->run();