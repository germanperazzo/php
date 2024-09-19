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
        $estrellas = (int)$data['estrellas'];
        $usuario_id = (int)$data['usuario_id'];
        $juego_id = (int)$data['juego_id'];

        if ($estrellas < 1 || $estrellas > 5) {
            $response->getBody()->write(json_encode(['message' => 'Número de estrellas inválido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ratingModel = new Rating();
        $ratingModel->createRating($estrellas, $usuario_id, $juego_id);

        $response->getBody()->write(json_encode(['message' => 'Calificación creada con éxito']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function updateRating(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $estrellas = (int)$data['estrellas'];

        if ($estrellas < 1 || $estrellas > 5) {
            $response->getBody()->write(json_encode(['message' => 'Número de estrellas inválido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ratingModel = new Rating();
        if ($ratingModel->updateRating($id, $estrellas)) {
            $response->getBody()->write(json_encode(['message' => 'Calificación actualizada con éxito']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['message' => 'No se pudo actualizar la calificación']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
    }

    public function deleteRating(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $ratingModel = new Rating();
        if ($ratingModel->deleteRating($id)) {
            $response->getBody()->write(json_encode(['message' => 'Calificación eliminada con éxito']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['message' => 'No se pudo eliminar la calificación']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
    }
}
