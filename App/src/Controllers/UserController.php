<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController
{
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

    public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = $data['clave'];

        // Add validation logic here
        $userModel = new User();
        $userModel->registerUser($nombre_usuario, $clave);

        $response->getBody()->write(json_encode(['message' => 'Usuario registrado con éxito']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }*/

    public function createUser(Request $request, Response $response)
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

        // Llamada al modelo para crear el usuario
        $result = $userModel->createUser($nombre_usuario, $clave, $es_admin);

        $payload = ['message' => 'Usuario creado exitosamente'] ;
        
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateUser(Request $request, Response $response, array $args)
    {   
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        $userModel = new User();
        // hacer una conecion con la base de datos y traer ven del token

        $nombre_usuario = $data['nombre_usuario'] ;
        $clave = $data['clave'] ;
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
    
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

    public function deleteUser(Request $request, Response $response, array $args)
    {   
        $data = $request->getParsedBody();
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
        //cheaer si esta log
        $id = $args['id'];
        $userModel = new User();
         //antes de llamar hayq  ver si tiene calificacion
        $result = $userModel->deleteUser($id);

        $payload = $result ? ['message' => 'Usuario eliminado correctamente'] : ['error' => 'Error al eliminar usuario'];

        $statusCode = $result ? 200 : 500;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function getUser(Request $request, Response $response, array $args)
    {   
        
        $data = $request->getParsedBody();
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
        //cheaer si esta log

        $id = $args['id'];
        $userModel = new User();

        
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

