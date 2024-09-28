<?php

use App\Controllers\UserController;
use App\Controllers\GameController;
use App\Controllers\RatingController;
use App\Middlewares\AuthMiddleware;


$app->post('/login', [UserController::class, 'login']);// hacer , crear token, 
$app->post('/register', [UserController::class, 'register']); //hacer , chequear todo,


//$app->add(AuthMiddleware::class);


$app->post('/usuario', [UserController::class, 'createUser']); 
$app->put('/usuario/{id}', [UserController::class, 'updateUser']);
$app->delete('/usuario/{id}', [UserController::class, 'deleteUser']);
$app->get('/usuario/{id}', [UserController::class, 'getUser']); //no entendi el tema de validar que el usuario este logueado


$app->get('/juegos', [GameController::class, 'listGames']);//como se ahce cual es la logica desde los parametros hasta hacer lo que tiene q ahcer
$app->get('/juegos/{id}', [GameController::class, 'getGame']);// falta traer el listaddo de calificaciones si tiene, como se ahce y como lo traigo al json
$app->post('/juego', [GameController::class, 'createGame']);// chear el token y que sea administrador.
$app->put('/juego/{id}', [GameController::class, 'updateGame']);// chear el token y que sea administrador.
$app->delete('/juego/{id}', [GameController::class, 'deleteGame']);// lo podes borrar si no tiene calificasioens y chear el token y que sea administrador.


$app->post('/calificacion', [RatingController::class, 'createRating']); // chear el token
$app->put('/calificacion/{id}', [RatingController::class, 'updateRating']); // no entendi la logica de los erroes y exito
$app->delete('/calificacion/{id}', [RatingController::class, 'deleteRating']);// chear el token

?>