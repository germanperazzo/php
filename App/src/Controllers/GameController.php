<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Game;

class GameController
{
    public function listGames(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $pagina = $params['pagina'] ?? 1;
        $clasificacion = $params['clasificacion'] ?? null;
        $texto = $params['texto'] ?? null;
        $plataforma = $params['plataforma'] ?? null;

        $gameModel = new Game();
        $games = $gameModel->listGames($pagina, $clasificacion, $texto, $plataforma);

        $response->getBody()->write(json_encode($games));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getGame(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $gameModel = new Game();
        $game = $gameModel->getGame($id);

        if ($game) {
            $response->getBody()->write(json_encode($game));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['message' => 'Juego no encontrado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    public function createGame(Request $request, Response $response)
    {
        // Similar to listGames, but for creating a game
    }

    public function updateGame(Request $request, Response $response, $args)
    {
        // Implement game update
    }

    public function deleteGame(Request $request, Response $response, $args)
    {
        // Implement game deletion
    }
}
