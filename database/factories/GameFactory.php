<?php

namespace Database\Factories;

use App\Models\GameCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Lista de nombres reales de juegos para hacerlo más realista
        $gameTitles = [
            'The Last Adventure', 'Dragon Quest Chronicles', 'Space Warriors',
            'Racing Legends', 'Fantasy Kingdom', 'Battle Royale Arena',
            'Mystery Island', 'Cyber Revolution', 'Ancient Legends', 'Storm Riders'
        ];

        return [
            'title' => fake()->randomElement($gameTitles) . ' ' . fake()->numberBetween(1, 10),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 9.99, 69.99), // Precios entre $9.99 y $69.99
            'release_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'developer' => fake()->company(),
            'publisher' => fake()->company(),
            'rating' => fake()->numberBetween(1, 10),
            'is_available' => fake()->boolean(85), // 85% de probabilidad de estar disponible
            'game_category_id' => GameCategory::factory(), // Crea automáticamente una categoría
        ];
    }
}
