<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteCollectorProxy;

use App\Controllers\Producto;


#aqui voy agragardo los endpoints de la api o rutas de la api 
$app->group('/api',function(RouteCollectorProxy $api){
    $api->group('/producto',function(RouteCollectorProxy $producto){
        $producto->get('[/read/{id}]',Producto::class .':read');//leer
        $producto->post('',Producto::class.':create'); //crear
        $producto->put('/{id}',Producto::class . ':update');//actualizar
        $producto->delete('/{id}',Producto::class . ':delete');//eliminar
        $producto->get('/filtrar',Producto::class . ':filtrar'); //filtrar
    });
});

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello World!");
    return $response;
});

