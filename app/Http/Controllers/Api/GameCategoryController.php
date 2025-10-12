<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GameCategoryController extends Controller
{
    /**
     * Muestra una lista de todas las categorías de juegos.
     * GET /api/game-categories
     */
    public function index(): JsonResponse
    {
        try {
            // Obtiene todas las categorías con sus juegos relacionados
            $categories = GameCategory::with('games')->get();
            
            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categorías obtenidas exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea una nueva categoría de juegos.
     * POST /api/game-categories
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validación de datos de entrada
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:game_categories,name',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Crear la categoría
            $category = GameCategory::create($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría creada exitosamente'
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
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una categoría específica.
     * GET /api/game-categories/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Busca la categoría o lanza error 404
            $category = GameCategory::with('games')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría obtenida exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza una categoría existente.
     * PUT/PATCH /api/game-categories/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $category = GameCategory::findOrFail($id);

            // Validación (unique excepto el registro actual)
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:game_categories,name,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Actualizar la categoría
            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría actualizada exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
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
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una categoría.
     * DELETE /api/game-categories/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = GameCategory::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
