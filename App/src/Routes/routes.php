<?php

use App\Controllers\UserController;
use App\Controllers\GameController;
use App\Controllers\RatingController;
use App\Middlewares\AuthMiddleware;

// Public routes
$app->post('/login', [UserController::class, 'login']);
$app->post('/register', [UserController::class, 'register']);

// Protected routes 
$app->add(AuthMiddleware::class);

// User routes
$app->post('/usuario', [UserController::class, 'createUser']);
$app->put('/usuario/{id}', [UserController::class, 'updateUser']);
$app->delete('/usuario/{id}', [UserController::class, 'deleteUser']);
$app->get('/usuario/{id}', [UserController::class, 'getUser']);

// Game routes
$app->get('/juegos', [GameController::class, 'listGames']);
$app->get('/juegos/{id}', [GameController::class, 'getGame']);
$app->post('/juego', [GameController::class, 'createGame']);
$app->put('/juego/{id}', [GameController::class, 'updateGame']);
$app->delete('/juego/{id}', [GameController::class, 'deleteGame']);

// Rating routes
$app->post('/calificacion', [RatingController::class, 'createRating']);
$app->put('/calificacion/{id}', [RatingController::class, 'updateRating']);
$app->delete('/calificacion/{id}', [RatingController::class, 'deleteRating']);

?>