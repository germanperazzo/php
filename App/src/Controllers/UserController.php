<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController
{
    public function login(Request $request, Response $response)
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
    }

    public function createUser(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $nombre_usuario = $data['nombre_usuario'];
        $clave = password_hash($data['clave'], PASSWORD_DEFAULT); // Encriptar la clave
        $es_admin = $data['es_admin'] ?? 0; // Por defecto no es admin
        $userModel = new User();

        // Llamada al modelo para crear el usuario
        $result = $userModel->createUser($nombre_usuario, $clave, $es_admin);

        $payload = $result ? ['message' => 'Usuario creado exitosamente'] : ['error' => 'Error al crear usuario'];

        // Especificar el código de estado
        $statusCode = $result ? 201 : 500;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function updateUser(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        // Extraer los parámetros opcionales
        $nombre_usuario = $data['nombre_usuario'] ?? null;
        $clave = isset($data['clave']) ? password_hash($data['clave'], PASSWORD_DEFAULT) : null;
        $token = $data['token'] ?? null;
        $vencimiento_token = $data['vencimiento_token'] ?? null;
        $es_admin = $data['es_admin'] ?? null;
        $userModel = new User();

        $result = $userModel->updateUser($id, $nombre_usuario, $clave, $token, $vencimiento_token, $es_admin);

        $payload = $result ? ['message' => 'Usuario actualizado exitosamente'] : ['error' => 'Error al actualizar usuario'];

        $statusCode = $result ? 200 : 500;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function deleteUser(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $userModel = new User();

        $result = $userModel->deleteUser($id);

        $payload = $result ? ['message' => 'Usuario eliminado correctamente'] : ['error' => 'Error al eliminar usuario'];

        $statusCode = $result ? 200 : 500;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function getUser(Request $request, Response $response, array $args)
    {
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

