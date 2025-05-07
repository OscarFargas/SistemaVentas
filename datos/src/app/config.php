<?php
$container->set('config_bd', function(){
    return (object)[
        "host"=>"db", // Si cambiamos el puerto hay que modificarlo
        "db"=>"taller", //esta es la base de datos
        "usr"=>"root",
        "passw"=>"12345",
        "charset"=>"utf8mb4"
    ];
});