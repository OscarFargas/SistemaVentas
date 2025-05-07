<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteCollectorProxy;

#use App\Controllers\Artefacto;


#aqui voy agragardo los endpoints de la api o rutas de la api 
$app->group('/api',function(RouteCollectorProxy $api){
    $api->group('/artefacto',function(RouteCollectorProxy $artefacto){
        $artefacto->get('[/read/{id}]',Artefacto::class .':read');//leer
        $artefacto->post('',Artefacto::class.':create'); //crear
        $artefacto->put('/{id}',Artefacto::class . ':update');//actualizar
        $artefacto->delete('/{id}',Artefacto::class . ':delete');//eliminar
        $artefacto->get('/filtrar',Artefacto::class . ':filtrar'); //filtrar
    });
});

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello World!");
    return $response;
});

