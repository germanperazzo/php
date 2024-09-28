<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Game;

class GameController
{
    /*public function listGames(Request $request, Response $response)
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
    }*/

    public function getGame(Request $request, Response $response, $args)
    {
        $id = $args['id'];

        $gameModel = new Game();
        $game = $gameModel->getGameById($id);
        //hacer otro select y despuesarmartodo
        if ($game) {
            $response->getBody()->write(json_encode($game));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['message' => 'Juego no encontrado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    public function createGame(Request $request, Response $response)
    {
    $data = $request->getParsedBody();
    //chear que este logeuado
    $token = $data['token'] ;
    $vencimiento_token = $data['vencimiento_token'];
    // chear que sea adm
    $es_admin = $data['es_admin'];
    
    $nombre = $data['nombre'];
    $descripcion = $data['descripcion'];
    $imagen = $data['imagen'];
    $clasificacion_edad= $data['clasificacion_edad'];
    
    $gameModel = new Game();

    $stmt = $gameModel->createGame($nombre,$descripcion,$imagen,$clasificacion_edad);
    

    $payload = $stmt ? ['message' => 'juego creado exitosamente'] : ['error' => 'Error al crear usuario'];

    $statusCode = $stmt ? 201 : 500;

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function updateGame(Request $request, Response $response, $args)
    {
    $data = $request->getParsedBody();
    //chear que este logeuado
    $token = $data['token'] ;
    $vencimiento_token = $data['vencimiento_token'];
    // chear que sea adm
    $es_admin = $data['es_admin'];

    $id = $args['id'];

    $nombre = $data['nombre'];
    $descripcion = $data['descripcion'];
    $imagen = $data['imagen'];
    $clasificacion_edad= $data['clasificacion_edad'];
    

      
    $gameModel = new Game();
    
    $stmt=$gameModel->updateGame($nombre,$descripcion,$imagen,$clasificacion_edad);

    $payload = $stmt ? ['message' => 'juego actualizado exitosamente'] : ['error' => 'Error al actualizar juego'];

    $statusCode = $stmt ? 200 : 500;

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);

    }

    public function deleteGame(Request $request, Response $response, $args)
    {   
        $data = $request->getParsedBody();
    //chear que este logeuado
    $token = $data['token'] ;
    $vencimiento_token = $data['vencimiento_token'];
    // chear que sea adm
    $es_admin = $data['es_admin'];

        $id = $args['id'];
    
        $gameModel = new Game();
        //chear que no tenga calificasiones;
        $stmt = $gameModel->deleteGame($id);
        
        
    
        $payload = $stmt ? ['message' => 'juego eliminado correctamente'] : ['error' => 'Error al eliminar juego'];
    
        $statusCode = $stmt ? 200 : 500;
    
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);  
    }
}
