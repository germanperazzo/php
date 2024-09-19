<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class AuthMiddleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        // Obtener el token de la cabecera Authorization
        $headers = $request->getHeaders();
        $token = $headers['HTTP_AUTHORIZATION'][0] ?? null;

        if ($token) {
            // Verificar la validez del token
            $userModel = new User();
            if ($userModel->isTokenValid($token)) {
                // Si el token es válido, pasar al siguiente middleware o controlador
                return $next($request, $response);
            }
        }

        // Si el token no es válido o no se proporciona, devolver error 401 Unauthorized
        $response->getBody()->write(json_encode(['message' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
}
