<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController
{   

    private function vefiricarLargoUsuario ($nombre_usuario){

        return (strlen($nombre_usuario) < 21) && (strlen($nombre_usuario)>5);
    }

    private function vefiricarLargoClave($clave){

        return strlen($clave)>7;
    }

    function validarClave($clave) {
        
        $lowcase = preg_match('/[a-z]/', $clave);
        
        $uppcase = preg_match('/[A-Z]/', $clave);
        
        $numbers = preg_match('/\d/', $clave);
        
        $special = preg_match('/[^a-zA-Z\d]/', $clave);
    
        
        return ($lowcase && $uppcase && $numbers && $special); 
        
    } 

    private function sesionVencida( $token, $id) {
        $userModel = new User();
        
        $tokenArray = $userModel->getToken($id);
        $tokenBaseDatos = $tokenArray['token'];
        $vencimiento_tokenBaseDatos = $tokenArray['vencimiento_token'];
        
        
        $hora_actual = date('Y-m-d H:i:s');
    
        
        return (($hora_actual > $vencimiento_tokenBaseDatos) || $tokenArray !=$token );
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave'];

        $userModel = new User();

        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        
        if($buscar_usuario->num_rows <= 0){

            $response->getBody()->write(json_encode(['message' => 'no  existe el usuario']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $x = $buscar_usuario->fetch_assoc();
        $claveBD = $x['clave'];
       
        if ($claveBD!=$clave) {
            $response->getBody()->write(json_encode(['message' => 'clave incorrecta']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        

        $bytes = random_bytes(64);
        $token = bin2hex($bytes);
        $vencimiento = date('Y-m-d H:i:s', strtotime('+1 hour'));


        $userModel()->updateToken($nombre_usuario, $token, $vencimiento);

        $response->getBody()->write(json_encode(['message' => '']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    
    
    /* echo*/public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave'];

        $userModel = new User();

        $tamaño_correcto = $this->vefiricarLargoUsuario($nombre_usuario);

        if(!$tamaño_correcto){
            $response->getBody()->write(json_encode(['error' => 'tamaño incorrerto del usuario']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        if(!ctype_alnum($nombre_usuario)){
            $response->getBody()->write(json_encode(['error' => 'el usuario no es alfanumerico']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); 

        }

        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        
        if($buscar_usuario->num_rows > 0){
            $payload =  ['message' => 'ya existe el usuario']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $tamaño_correcto = $this->vefiricarLargoClave($clave);

        if(!$tamaño_correcto){
            $response->getBody()->write(json_encode(['error' => 'tamaño incorrerto de la clave']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $validar_clave = $this->validarClave($clave);

        if(!$validar_clave){
            
            $response->getBody()->write(json_encode(['message' => 'Clave  no cumple condiciones']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $userModel->registerUser($nombre_usuario, $clave);

        $response->getBody()->write(json_encode(['message' => 'Usuario registrado con éxito']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /* echo*/public function createUser(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave']; 
        $es_admin = $data['es_admin']; 
        $userModel = new User();
        

        $tamaño_correcto = $this->vefiricarLargoUsuario($nombre_usuario);

        if(!$tamaño_correcto){
            $response->getBody()->write(json_encode(['error' => 'tamaño incorrerto del usuario']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        if(!ctype_alnum($nombre_usuario)){
            $response->getBody()->write(json_encode(['error' => 'el usuario no es alfanumerico']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); 

        }

        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        
        if($buscar_usuario->num_rows > 0){
            $payload =  ['message' => 'ya existe el usuario']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $tamaño_correcto = $this->vefiricarLargoClave($clave);

        if(!$tamaño_correcto){
            $response->getBody()->write(json_encode(['error' => 'tamaño incorrerto de la clave']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $validar_clave = $this->validarClave($clave);

        if(!$validar_clave){
            
            $response->getBody()->write(json_encode(['message' => 'Clave  no cumple condiciones']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        

        $result = $userModel->createUser($nombre_usuario, $clave, $es_admin);

        $response->getBody()->write(json_encode(['message' => 'Usuario creado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /* echo*/public function updateUser(Request $request, Response $response, array $args)
    {   
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        $userModel = new User();
        

        $nombre_usuario = $data['nombre_usuario'] ;
        $clave = $data['clave'] ;
        $token = $data['token'];
        

        if($this->sesionVencida( $token, $id)){

            $payload = ['error' => 'secion vencida'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } 

        $tamaño_correcto = $this->vefiricarLargoUsuario($nombre_usuario);

        if(!$tamaño_correcto){
            $response->getBody()->write(json_encode(['error' => 'tamaño incorrerto del usuario']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        if(!ctype_alnum($nombre_usuario)){
            $response->getBody()->write(json_encode(['error' => 'el usuario no es alfanumerico']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); 

        }

        //buscar si existe el usuario

        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        
        if($buscar_usuario->num_rows > 0){
            $payload =  ['message' => 'ya existe el usuario']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $tamaño_correcto = $this->vefiricarLargoClave($clave);

        if(!$tamaño_correcto){
            $response->getBody()->write(json_encode(['error' => 'tamaño incorrerto de la clave']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $validar_clave = $this->validarClave($clave);

        if(!$validar_clave){
            
            $response->getBody()->write(json_encode(['message' => 'Clave  no cumple condiciones']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }        

        $userModel->updateUser($id, $nombre_usuario, $clave);


        $response->getBody()->write(json_encode(['message' => 'Usuario actualizado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /* echo*/public function deleteUser(Request $request, Response $response, array $args)
    {   
        $data = $request->getParsedBody();
        $token = $data['token'] ;
        
       
        $id = $args['id'];
        //cheaer si esta log
        $userModel = new User();
        

        if($this->sesionVencida( $token, $id)){

            $payload = ['error' => 'secion vencida'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } 
        
        
        $buscarCalificaciones = $userModel()->getUserCalificaciones($id);
        
        if($buscarCalificaciones->num_rows > 0){
            $payload =  ['message' => 'no se puede borrar usuario pro q tiene calificaciones']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $result = $userModel->deleteUser($id);

        $payload = $result ? ['message' => 'Usuario eliminado correctamente'] : ['error' => 'Error al eliminar usuario'];

        $statusCode = $result ? 200 : 400;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    /* echo*/public function getUser(Request $request, Response $response, array $args)
    {   
        
        $data = $request->getParsedBody();
        $token = $data['token'] ;
        
        //cheaer si esta log

        $id = $args['id'];
        $userModel = new User();
        
        if($this->sesionVencida( $token, $id)){

            $payload = ['error' => 'secion vencida'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } 
        
        $user = $userModel->getUserById($id);

        if ($user) {
            $response->getBody()->write(json_encode($user));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Usuario no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}

