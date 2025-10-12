<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    /**
     * Muestra una lista de todos los juegos.
     * GET /api/games
     */
    public function index(): JsonResponse
    {
        try {
            // Obtiene todos los juegos con su categoría relacionada
            $games = Game::with('gameCategory')->get();
            
            return response()->json([
                'success' => true,
                'data' => $games,
                'message' => 'Juegos obtenidos exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los juegos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un nuevo juego.
     * POST /api/games
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validación de datos de entrada
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0|max:999999.99',
                'release_date' => 'nullable|date',
                'developer' => 'nullable|string|max:255',
                'publisher' => 'nullable|string|max:255',
                'rating' => 'nullable|integer|min:1|max:10',
                'is_available' => 'boolean',
                'game_category_id' => 'required|exists:game_categories,id',
            ]);

            // Crear el juego
            $game = Game::create($validated);
            
            // Cargar la relación de categoría
            $game->load('gameCategory');

            return response()->json([
                'success' => true,
                'data' => $game,
                'message' => 'Juego creado exitosamente'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el juego',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra un juego específico.
     * GET /api/games/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Busca el juego o lanza error 404
            $game = Game::with('gameCategory')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $game,
                'message' => 'Juego obtenido exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el juego',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza un juego existente.
     * PUT/PATCH /api/games/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $game = Game::findOrFail($id);

            // Validación
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0|max:999999.99',
                'release_date' => 'nullable|date',
                'developer' => 'nullable|string|max:255',
                'publisher' => 'nullable|string|max:255',
                'rating' => 'nullable|integer|min:1|max:10',
                'is_available' => 'boolean',
                'game_category_id' => 'sometimes|required|exists:game_categories,id',
            ]);

            // Actualizar el juego
            $game->update($validated);
            
            // Recargar la relación
            $game->load('gameCategory');

            return response()->json([
                'success' => true,
                'data' => $game,
                'message' => 'Juego actualizado exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el juego',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un juego.
     * DELETE /api/games/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $game = Game::findOrFail($id);
            $game->delete();

            return response()->json([
                'success' => true,
                'message' => 'Juego eliminado exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el juego',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
