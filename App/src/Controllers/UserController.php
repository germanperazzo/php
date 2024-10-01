<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController
{
     private function calcularDiferenciaSegundos($vencimiento_token, $token, $id) {
        $userModel = new User();
        
        $tokenArray = $userModel->getToken($id);
        $tokenBaseDatos = $tokenArray['token'];
        $vencimiento_tokenBaseDatos = $tokenArray['vencimiento_token'];
        
        // fechas a objetos DateTime
        $jsonDate = new \DateTime($vencimiento_token);
        $dbDate = new \DateTime($vencimiento_tokenBaseDatos);
    
        // Calcula la diferencia 
        $interval = $dbDate->diff($jsonDate);
    
        // Convertir  a segundos
        $diferenciaSegundos = ($interval->days * 24 * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
    
        
        return ($token != $tokenBaseDatos)&&($diferenciaSegundos > 3600);
    }

    /*public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave'];

        $userModel = new User();
        $user = $userModel->authenticate($nombre_usuario, $clave);

        if ($user) {
            $token = bin2hex(random_bytes(16)); // Generate token
            $vencimiento_token = (new \DateTime())->add(new \DateInterval('PT1H'))->format('Y-m-d H:i:s');
            $userModel->updateToken($user['id'], $token, $vencimiento_token);

            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['message' => 'Credenciales inválidas']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    */
    /* echo*/public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave'];

        $chequeo_pass = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

        if(!((strlen($nombre_usuario) < 21) && (strlen($nombre_usuario)>5) && (preg_match($chequeo_pass, $clave)) && (strlen($clave) > 7))){
            $payload =  ['message' => 'Clave o usuario no cumple condiciones']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $userModel = new User();
        
        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        $existe_usuario = $buscar_usuario->get_result();
        if($existe_usuario->num_rows > 0){
            $payload =  ['message' => 'ya existe el usuario']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $bytes = random_bytes(64);
        $token = bin2hex($bytes);

        $userModel->registerUser($nombre_usuario, $clave, $token);

        $response->getBody()->write(json_encode(['message' => 'Usuario registrado con éxito']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /* echo*/public function createUser(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave']; 
        $es_admin = $data['es_admin']; 
        $userModel = new User();
        

        //chequeo 
        $chequeo_pass = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

        if(!((strlen($nombre_usuario) < 21) && (strlen($nombre_usuario)>5) && (preg_match($chequeo_pass, $clave)) && (strlen($clave) > 7))){
            $payload =  ['message' => 'Clave o usuario no cumple condiciones']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        //buscar si existe el usuario

        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        $existe_usuario = $buscar_usuario->get_result();
        if($existe_usuario->num_rows > 0){
            $payload =  ['message' => 'ya existe el usuario']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $bytes = random_bytes(64);
        $token = bin2hex($bytes);

        // Llamada al modelo para crear el usuario, hay q mandarle el token
        $result = $userModel->createUser($nombre_usuario, $clave, $es_admin);

        $payload = ['message' => 'Usuario creado exitosamente'] ;
        
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /* echo*/public function updateUser(Request $request, Response $response, array $args)
    {   
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        $userModel = new User();
        

        $nombre_usuario = $data['nombre_usuario'] ;
        $clave = $data['clave'] ;
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
        
        

        if($this->calcularDiferenciaSegundos($vencimiento_token, $token,$id)){

            $payload = ['error' => 'secion vencida'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } 

        //chequeo 
        $chequeo_pass = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

        if(!((strlen($nombre_usuario) < 21) && (strlen($nombre_usuario)>5) && (preg_match($chequeo_pass, $clave)) && (strlen($clave) > 7))){
            $payload =  ['message' => 'Clave o usuario no cumple condiciones']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        //buscar si existe el usuario

        $buscar_usuario = $userModel()->getUserByUsuario($nombre_usuario);
        $existe_usuario = $buscar_usuario->get_result();
        if($existe_usuario->num_rows > 0){
            $payload =  ['message' => 'ya existe el usuario']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $result = $userModel->updateUser($id, $nombre_usuario, $clave);

        $payload = ['message' => 'Usuario actualizado exitosamente'];

        

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /* echo*/public function deleteUser(Request $request, Response $response, array $args)
    {   
        $data = $request->getParsedBody();
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
       
        $id = $args['id'];
        //cheaer si esta log
        $userModel = new User();
        

        if($this->calcularDiferenciaSegundos($vencimiento_token, $token,$id)){

            $payload = ['error' => 'secion vencida'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } 
        
        
        $buscarCalificaciones = $userModel()->getUserCalificaciones($id);
        $existeCalificacion = $buscarCalificaciones->get_result();
        if($existeCalificacion->num_rows > 0){
            $payload =  ['message' => 'no se puede borrar usuario pro q tiene calificaciones']; 
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

        }

        $result = $userModel->deleteUser($id);

        $payload = $result ? ['message' => 'Usuario eliminado correctamente'] : ['error' => 'Error al eliminar usuario'];

        $statusCode = $result ? 200 : 500;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    /* echo*/public function getUser(Request $request, Response $response, array $args)
    {   
        
        $data = $request->getParsedBody();
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
        //cheaer si esta log

        $id = $args['id'];
        $userModel = new User();
        echo "hola";
        if($this->calcularDiferenciaSegundos($vencimiento_token, $token,$id)){
            echo "hola2";
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

