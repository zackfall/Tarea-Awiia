<?php

use App\Models\GameCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * RefreshDatabase ejecuta las migraciones antes de cada prueba
 * y las revierte después, manteniendo la BD limpia
 */
uses(RefreshDatabase::class);

/**
 * PRUEBAS PARA LISTAR CATEGORÍAS (GET /api/game-categories)
 */
test('puede listar todas las categorías', function () {
    // Arrange: Crear 5 categorías de prueba
    GameCategory::factory()->count(5)->create();

    // Act: Hacer petición GET
    $response = $this->getJson('/api/game-categories');

    // Assert: Verificar respuesta
    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at']
            ],
            'message'
        ])
        ->assertJsonPath('success', true)
        ->assertJsonCount(5, 'data');
});

test('lista de categorías incluye juegos relacionados', function () {
    $category = GameCategory::factory()
        ->hasGames(3) // Crear 3 juegos para esta categoría
        ->create();

    $response = $this->getJson('/api/game-categories');

    $response->assertStatus(200)
        ->assertJsonPath('data.0.games', fn($games) => count($games) === 3);
});

/**
 * PRUEBAS PARA CREAR CATEGORÍAS (POST /api/game-categories)
 */
test('puede crear una nueva categoría', function () {
    $categoryData = [
        'name' => 'Acción',
        'description' => 'Juegos de acción y combate',
        'is_active' => true
    ];

    $response = $this->postJson('/api/game-categories', $categoryData);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Acción')
        ->assertJsonPath('data.is_active', true);

    // Verificar que se guardó en la base de datos
    $this->assertDatabaseHas('game_categories', [
        'name' => 'Acción',
        'description' => 'Juegos de acción y combate'
    ]);
});

test('falla al crear categoría sin nombre', function () {
    $response = $this->postJson('/api/game-categories', [
        'description' => 'Sin nombre'
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors(['name']);
});

test('falla al crear categoría con nombre duplicado', function () {
    GameCategory::factory()->create(['name' => 'RPG']);

    $response = $this->postJson('/api/game-categories', [
        'name' => 'RPG',
        'description' => 'Juegos de rol'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

/**
 * PRUEBAS PARA VER UNA CATEGORÍA (GET /api/game-categories/{id})
 */
test('puede ver una categoría específica', function () {
    $category = GameCategory::factory()->create(['name' => 'Aventura']);

    $response = $this->getJson("/api/game-categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Aventura')
        ->assertJsonPath('data.id', $category->id);
});

test('retorna 404 al buscar categoría inexistente', function () {
    $response = $this->getJson('/api/game-categories/999');

    $response->assertStatus(404)
        ->assertJsonPath('success', false);
});

/**
 * PRUEBAS PARA ACTUALIZAR CATEGORÍAS (PUT /api/game-categories/{id})
 */
test('puede actualizar una categoría', function () {
    $category = GameCategory::factory()->create(['name' => 'RPG']);

    $response = $this->putJson("/api/game-categories/{$category->id}", [
        'name' => 'RPG Actualizado',
        'is_active' => false
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'RPG Actualizado')
        ->assertJsonPath('data.is_active', false);

    $this->assertDatabaseHas('game_categories', [
        'id' => $category->id,
        'name' => 'RPG Actualizado',
        'is_active' => false
    ]);
});

test('falla al actualizar con nombre duplicado', function () {
    GameCategory::factory()->create(['name' => 'Acción']);
    $category = GameCategory::factory()->create(['name' => 'Deportes']);

    $response = $this->putJson("/api/game-categories/{$category->id}", [
        'name' => 'Acción'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

/**
 * PRUEBAS PARA ELIMINAR CATEGORÍAS (DELETE /api/game-categories/{id})
 */
test('puede eliminar una categoría', function () {
    $category = GameCategory::factory()->create();

    $response = $this->deleteJson("/api/game-categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('game_categories', [
        'id' => $category->id
    ]);
});

test('retorna 404 al eliminar categoría inexistente', function () {
    $response = $this->deleteJson('/api/game-categories/999');

    $response->assertStatus(404)
        ->assertJsonPath('success', false);
});

/**
 * PRUEBAS DE RELACIONES
 */
test('al eliminar categoría se eliminan sus juegos (cascade)', function () {
    $category = GameCategory::factory()
        ->hasGames(3)
        ->create();

    $gameIds = $category->games->pluck('id')->toArray();

    $this->deleteJson("/api/game-categories/{$category->id}");

    // Verificar que los juegos también se eliminaron
    foreach ($gameIds as $gameId) {
        $this->assertDatabaseMissing('games', ['id' => $gameId]);
    }
});
