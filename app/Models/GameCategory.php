<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameCategory extends Model
{
    /** @use HasFactory<\Database\Factories\GameCategoryFactory> */
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * Esto permite hacer GameCategory::create(['name' => 'Acción', ...])
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Convierte '1' de la BD a true/false en PHP
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtiene todos los juegos que pertenecen a esta categoría.
     * Relación: Una categoría tiene muchos juegos (One-to-Many)
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
