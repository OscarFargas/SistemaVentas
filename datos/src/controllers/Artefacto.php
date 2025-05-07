<?php
    namespace App\Controllers;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Container\ContainerInterface;
    
    use PDO;

    class Artefacto{
        protected $container;
        
        public function __construct(ContainerInterface $c){
            $this->container = $c;
        }

        public function read(Request $request, Response $response, $args){
          $sql = "SELECT * FROM Artefacto ";

          if(isset($args["id"])){
            $sql.="WHERE id=:id ";
          }

          $sql.="LIMIT 0, 5";

          $con = $this->container->get('base_datos');
          $query = $con->prepare($sql);
          
          if(isset($args["id"])){
            $query->execute(["id"=>$args["id"]]);
          }else{
            $query->execute();
          }
          

          $res = $query->fetchAll();
          $status=$query->rowCount()>0?200:204;
          $query=null;
          $con=null;  

          
          //Obtener un a respuesta
          $response->getBody()->write(json_encode($res));
        
            return $response
            ->withHeader('Content-type','Application/json')
            ->withStatus($status);
        }

        public function create(Request $request, Response $response, $args){
            $body = json_decode($request->getBody());
            $campos ="";
            $params="";

            //FOR EACH PARA CAMPOS
            foreach($body as $key => $value){
              $campos .=$key .', ';
              $params .=":".$key .', ';
            };
            $campos= substr($campos,0,-2);
          
            $params=substr($params,0,-2);

            $sql= "INSERT INTO Artefacto($campos) VALUES($params);";
            $con =$this->container->get('base_datos');
            $con->beginTransaction();

            $query = $con->prepare($sql);
            
            foreach($body as $key => $value){
              $TIPO=gettype($value)=='integer'? PDO::PARAM_INT : PDO::PARAM_STR;
              $value = filter_var($value,FILTER_SANITIZE_SPECIAL_CHARS);
              $query->bindValue($key,$value,$TIPO);
            };
            try{
              $query->execute();

              $con->commit();
            }catch(PDOException $e){
              $status=$e.getCode()==23000?409:500;
              $con->rollback();
            }

            $status =$query->rowCount()>0?201:409;
            $query=null;
            $con=null;
           return $response->withStatus($status);
        }

        public function update(Request $request, Response $response, $args){
          $body = json_decode($request->getBody());
          $id=$args["id"];
        
          if (isset($body->id)){
            unset($body->id);
          }
          if (isset($body->codigo_producto)) {
            unset($body->codigo_producto);
          }

          var_dump($body);

          $sql= "UPDATE artefacto SET ";
          foreach($body as $key =>$value){
            $sql .= "$key = :$key, ";
          }
          $sql=substr($sql,0,-2);
          $sql.= " WHERE id=:id;";

          $con =$this->container->get('base_datos');
          $query = $con->prepare($sql);

          foreach($body as $key => $value){
            $TIPO=gettype($value)=='integer'? PDO::PARAM_INT : PDO::PARAM_STR;
            $value = filter_var($value,FILTER_SANITIZE_SPECIAL_CHARS);
            $query->bindValue($key,$value,$TIPO);
          };

          $query->bindValue("id",$id,PDO::PARAM_INT);//Se le da el parametro id
          $query->execute();
          $status =$query->rowCount()>0?200:204;//200 creado; 204 error por lado del cliente
          $query=null;
          $con=null;
         return $response->withStatus($status);
      }

      public function delete(Request $request, Response $response, $args){
        $sql = "DELETE FROM artefacto WHERE id = :id;";
        $con = $this->container->get('base_datos');

        $query = $con->prepare($sql);

        $query->bindValue("id", $args ["id"], PDO::PARAM_INT);
        $query->execute();
       
        $status = $query->rowCount() > 0 ? 200: 404;

        $query = null;
        $con = null;

        return $response->withStatus($status);
     }

     public function filtrar(Request $request, Response $response, $args){
      $datos = $request->getQueryParams();

    
      $sql = "SELECT * FROM artefacto WHERE ";
      foreach ($datos as $key => $value) {
        $sql .="$key LIKE :$key AND ";
      }
      $sql=rtrim($sql,'AND ').";";


      //$sql.="LIMIT 0, 5";

      $con = $this->container->get('base_datos');
      $query = $con->prepare($sql);
      
      foreach ($datos as $key => $value) {
        $query->bindValue(":$key","%$value%",PDO::PARAM_STR);
      }

      $query->execute();

      $res = $query->fetchAll();
      $status=$query->rowCount()>0?200:204;

      $query=null;
      $con=null;  

      
      //Obtener un a respuesta
      $response->getBody()->write(json_encode($res));
    
        return $response
        ->withHeader('Content-type','Application/json')
        ->withStatus($status);
    }
    }