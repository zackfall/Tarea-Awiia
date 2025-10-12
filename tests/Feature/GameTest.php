<?php

use App\Models\Game;
use App\Models\GameCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * PRUEBAS PARA LISTAR JUEGOS (GET /api/games)
 */
test('puede listar todos los juegos', function () {
    // Crear juegos con sus categorías
    Game::factory()->count(5)->create();

    $response = $this->getJson('/api/games');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id', 'title', 'description', 'price', 'release_date',
                    'developer', 'publisher', 'rating', 'is_available',
                    'game_category_id', 'created_at', 'updated_at'
                ]
            ],
            'message'
        ])
        ->assertJsonPath('success', true)
        ->assertJsonCount(5, 'data');
});

test('lista de juegos incluye categoría relacionada', function () {
    $category = GameCategory::factory()->create(['name' => 'Acción']);
    Game::factory()->create(['game_category_id' => $category->id]);

    $response = $this->getJson('/api/games');

    $response->assertStatus(200)
        ->assertJsonPath('data.0.game_category.name', 'Acción');
});

/**
 * PRUEBAS PARA CREAR JUEGOS (POST /api/games)
 */
test('puede crear un nuevo juego', function () {
    $category = GameCategory::factory()->create();

    $gameData = [
        'title' => 'The Last of Us',
        'description' => 'Un juego de supervivencia post-apocalíptico',
        'price' => 59.99,
        'release_date' => '2023-06-14',
        'developer' => 'Naughty Dog',
        'publisher' => 'Sony',
        'rating' => 10,
        'is_available' => true,
        'game_category_id' => $category->id
    ];

    $response = $this->postJson('/api/games', $gameData);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'The Last of Us')
        ->assertJsonPath('data.price', '59.99')
        ->assertJsonPath('data.rating', 10);

    $this->assertDatabaseHas('games', [
        'title' => 'The Last of Us',
        'developer' => 'Naughty Dog'
    ]);
});

test('falla al crear juego sin título', function () {
    $category = GameCategory::factory()->create();

    $response = $this->postJson('/api/games', [
        'price' => 29.99,
        'game_category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors(['title']);
});

test('falla al crear juego sin precio', function () {
    $category = GameCategory::factory()->create();

    $response = $this->postJson('/api/games', [
        'title' => 'Juego sin precio',
        'game_category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['price']);
});

test('falla al crear juego sin categoría', function () {
    $response = $this->postJson('/api/games', [
        'title' => 'Juego sin categoría',
        'price' => 39.99
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['game_category_id']);
});

test('falla al crear juego con categoría inexistente', function () {
    $response = $this->postJson('/api/games', [
        'title' => 'Juego test',
        'price' => 39.99,
        'game_category_id' => 999 // ID que no existe
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['game_category_id']);
});

test('falla al crear juego con precio negativo', function () {
    $category = GameCategory::factory()->create();

    $response = $this->postJson('/api/games', [
        'title' => 'Juego test',
        'price' => -10.00,
        'game_category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['price']);
});

test('falla al crear juego con rating fuera de rango', function () {
    $category = GameCategory::factory()->create();

    $response = $this->postJson('/api/games', [
        'title' => 'Juego test',
        'price' => 39.99,
        'rating' => 15, // Máximo es 10
        'game_category_id' => $category->id
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['rating']);
});

/**
 * PRUEBAS PARA VER UN JUEGO (GET /api/games/{id})
 */
test('puede ver un juego específico', function () {
    $game = Game::factory()->create(['title' => 'God of War']);

    $response = $this->getJson("/api/games/{$game->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'God of War')
        ->assertJsonPath('data.id', $game->id);
});

test('retorna 404 al buscar juego inexistente', function () {
    $response = $this->getJson('/api/games/999');

    $response->assertStatus(404)
        ->assertJsonPath('success', false);
});

/**
 * PRUEBAS PARA ACTUALIZAR JUEGOS (PUT /api/games/{id})
 */
test('puede actualizar un juego', function () {
    $game = Game::factory()->create(['title' => 'Halo', 'price' => 49.99]);

    $response = $this->putJson("/api/games/{$game->id}", [
        'title' => 'Halo Infinite',
        'price' => 59.99,
        'rating' => 9
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'Halo Infinite')
        ->assertJsonPath('data.price', '59.99')
        ->assertJsonPath('data.rating', 9);

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'title' => 'Halo Infinite',
        'price' => 59.99
    ]);
});

test('puede actualizar solo algunos campos del juego', function () {
    $game = Game::factory()->create([
        'title' => 'Minecraft',
        'price' => 26.95,
        'is_available' => true
    ]);

    $response = $this->putJson("/api/games/{$game->id}", [
        'is_available' => false
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.is_available', false)
        ->assertJsonPath('data.title', 'Minecraft'); // Se mantiene el título original
});

test('puede cambiar la categoría de un juego', function () {
    $oldCategory = GameCategory::factory()->create(['name' => 'Acción']);
    $newCategory = GameCategory::factory()->create(['name' => 'RPG']);
    
    $game = Game::factory()->create(['game_category_id' => $oldCategory->id]);

    $response = $this->putJson("/api/games/{$game->id}", [
        'game_category_id' => $newCategory->id
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.game_category.name', 'RPG');
});

/**
 * PRUEBAS PARA ELIMINAR JUEGOS (DELETE /api/games/{id})
 */
test('puede eliminar un juego', function () {
    $game = Game::factory()->create();

    $response = $this->deleteJson("/api/games/{$game->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('games', [
        'id' => $game->id
    ]);
});

test('retorna 404 al eliminar juego inexistente', function () {
    $response = $this->deleteJson('/api/games/999');

    $response->assertStatus(404)
        ->assertJsonPath('success', false);
});

/**
 * PRUEBAS DE RELACIONES
 */
test('juego mantiene relación con su categoría', function () {
    $category = GameCategory::factory()->create(['name' => 'Deportes']);
    $game = Game::factory()->create(['game_category_id' => $category->id]);

    // Refrescar para cargar la relación
    $game->load('gameCategory');

    expect($game->gameCategory)
        ->not->toBeNull()
        ->and($game->gameCategory->name)->toBe('Deportes');
});

test('múltiples juegos pueden pertenecer a la misma categoría', function () {
    $category = GameCategory::factory()->create();
    
    Game::factory()->count(5)->create(['game_category_id' => $category->id]);

    $category->load('games');

    expect($category->games)->toHaveCount(5);
});
