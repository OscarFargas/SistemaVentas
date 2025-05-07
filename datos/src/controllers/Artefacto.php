<?php 
namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use PDO;

class Artefacto{

    protected $container;

   
    public function __construct(ContainerInterface $container) {
        $this->container = $container; 
    }
    #/ Método para crear un nuevo producto request la consulta,args agumentos
     //401 no cntra ni usuario// 403 no tiene derecho //error de usuario inician con 4 // generando un conflico como crear doble id o algo asi 
    
    #/ Método para crear un nuevo producto request la consulta,args agumentos
     //401 no cntra ni usuario// 403 no tiene derecho //error de usuario inician con 4 // generando un conflico como crear doble id o algo asi 
     public function read(Request $request, Response $response, $args)
     {
         $sql = "SELECT * FROM artefacto";
      
         //si tiene un arigumento id en la url entonces se le agrega a la consulta sql
         if(isset($args['id'])){
            $sql.=" WHERE id = :id;";
         }
         $sql.=" LIMIT 0,5;";

         $con = $this->container->get('base_datos');
         $query = $con->prepare($sql);//el query recibe la consulta sql 



         if(isset($args["id"])){
            $query->execute(["id"=>$args["id"]]);
         }else{
            $query->execute();
         }
       
         $res = $query->fetchAll();

 
         $status= $query->rowCount() > 0 ? 200 : 204 ;
 
         $response->getBody()->write(json_encode($res));
         return $response
             ->withHeader('Content-Type', 'application/json')
             ->withStatus($status);
 
     }


    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
       
        
        $campos = "";
        foreach ($body as $key => $value) {
            $campos .= $key . ", ";
        };
        $campos = substr($campos, 0, -2); //Eliminar la última coma y espacio


        $params = ""; 
        foreach ($body as $key => $value) {
            $params .= ":" . $key . ", ";
        };
        $params = substr($params, 0, -2); //Eliminar la última coma y espacio
        
        $sql = "INSERT INTO artefacto($campos)  VALUES ($params);";

        //die($sql);

        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        foreach($body as $key => $value){
            $TIPO= gettype($value)=="integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
            $query->bindValue($key, $value, $TIPO);
        };

        try
        {
            $query->execute();
            $cont->commit();
            $status=201;
        } catch (\PDOException $e) {
            $status=$e.get_code()==23000 ? 409 : 500; //409 Conflicto, 500 Error interno del servidor
            $cont->rollBack();
        
        }

       

        $status = $query->rowCount() > 0 ? 201 : 409; //201 Creado, 409 Conflicto

        $query = null; //Cerrar la consulta
        $con = null; //Cerrar la conexión

        return $response->withStatus($status);
    }

    
    public function update(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
    
        
        if(isset($body->id)){
            unset($body->id);
        }

        if(isset($body->codigo_producto)){
            unset($body->codigo_producto);
        }

        $sql = "UPDATE artefacto SET";

        foreach($body as $key => $value){
            $sql .= " $key = :$key, " ;
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE id = :id;";

        $con = $this->container->get('base_datos');
        $query= $con->prepare($sql);

        foreach($body as $key => $value){
            $TIPO = gettype($value) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            $query->bindValue(":$key", $value, $TIPO); 
        }

        $query->bindValue(":id",$args["id"], PDO::PARAM_INT);
        $query->execute();

        $status= $query->rowCount() > 0 ? 200 : 204;
        $query=null;
        $con=null;

        return $response->withStatus($status);
    }
    public function delete(Request $request, Response $response, $args){
        $sql = "DELETE FROM artefacto WHERE id = :id";
        $con = $this->container->get('base_datos');
        $id = $args["id"];
        
        $query = $con->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    public function filtrar(Request $request, Response $response, $args){
        $sql = "SELECT * FROM artefacto WHERE ";
        $datos = $request->getQueryParams();
        foreach ($datos as $key => $value) {
            $sql .= "$key LIKE :$key AND ";
        }
        $sql = rtrim($sql, 'AND ') . ';';
        

        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        foreach ($datos as $key => $value) {
            $query->bindValue(":$key", "%$value%", PDO::PARAM_STR);
        }
        $query->execute();

        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($res));

        $query = null;
        $con = null;

        return $response
        ->withHeader('Content-type', 'Application/json')
        ->withstatus($status);
    }
}
