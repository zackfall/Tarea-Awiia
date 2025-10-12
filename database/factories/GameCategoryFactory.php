<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameCategory>
 */
class GameCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Lista de categorías reales de videojuegos
        $categories = [
            'Acción', 'Aventura', 'RPG', 'Estrategia', 'Simulación',
            'Deportes', 'Carreras', 'Puzzle', 'Horror', 'Plataformas'
        ];

        return [
            'name' => fake()->unique()->randomElement($categories),
            'description' => fake()->sentence(15),
            'is_active' => fake()->boolean(90), // 90% de probabilidad de estar activo
        ];
    }
}
