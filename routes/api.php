<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\GameCategoryController;

/**
 * Rutas de la API para Game Categories
 * Todas estas rutas están prefijadas con /api/game-categories
 */
Route::apiResource('game-categories', GameCategoryController::class);

/**
 * Rutas de la API para Games
 * Todas estas rutas están prefijadas con /api/games
 */
Route::apiResource('games', GameController::class);

/**
 * EXPLICACIÓN DE apiResource:
 * 
 * Esta función crea automáticamente 5 rutas para cada recurso:
 * 
 * Para game-categories:
 * - GET    /api/game-categories        -> index()   (listar todas)
 * - POST   /api/game-categories        -> store()   (crear nueva)
 * - GET    /api/game-categories/{id}   -> show()    (ver una)
 * - PUT    /api/game-categories/{id}   -> update()  (actualizar)
 * - DELETE /api/game-categories/{id}   -> destroy() (eliminar)
 * 
 * Para games:
 * - GET    /api/games        -> index()
 * - POST   /api/games        -> store()
 * - GET    /api/games/{id}   -> show()
 * - PUT    /api/games/{id}   -> update()
 * - DELETE /api/games/{id}   -> destroy()
 */
