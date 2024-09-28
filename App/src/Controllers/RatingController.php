<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Rating;

class RatingController
{
    public function createRating(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
         //chear que este logeuado
    $token = $data['token'] ;
    $vencimiento_token = $data['vencimiento_token'];

        $estrellas = (int) $data['estrellas'];
        $usuario_id = (int) $data['usuario_id'];
        $juego_id = (int) $data['juego_id'];

        // Validar número de estrellas
        if ($estrellas < 1 || $estrellas > 5) {
            $payload = ['message' => 'Número de estrellas inválido'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ratingModel = new Rating();
        $result = $ratingModel->createRating($estrellas, $usuario_id, $juego_id);

        // Respuesta en caso de éxito o error
        $payload = $result ? ['message' => 'Calificación creada con éxito'] : ['message' => 'Error al crear la calificación'];
        $statusCode = $result ? 201 : 500;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function updateRating(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
         //chear que este logeuado
        $token = $data['token'] ;
        $vencimiento_token = $data['vencimiento_token'];
        
        $estrellas = (int) $data['estrellas'];


        // Validar número de estrellas
        if ($estrellas < 1 || $estrellas > 5) {
            $payload = ['message' => 'Número de estrellas inválido'];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ratingModel = new Rating();
        $result = $ratingModel->updateRating($id, $estrellas);

        // Respuesta en caso de éxito o error
        $payload = ['message' => 'Calificación actualizada con éxito'];
       

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteRating(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
         //chear que este logeuado
         $data = $request->getParsedBody();
         $token = $data['token'] ;
         $vencimiento_token = $data['vencimiento_token'];

        $ratingModel = new Rating();
        $result = $ratingModel->deleteRating($id);

        // Respuesta en caso de éxito o error
        $payload = $result ? ['message' => 'Calificación eliminada con éxito'] : ['message' => 'No se pudo eliminar la calificación'];
        $statusCode = $result ? 200 : 409;

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}
