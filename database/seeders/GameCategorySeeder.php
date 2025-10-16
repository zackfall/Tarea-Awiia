<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GameCategory;
use App\Models\Game;

class GameCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GameCategory::factory()
            ->count(5)
            ->has(Game::factory()->count(8))
            ->create();
    }
}
