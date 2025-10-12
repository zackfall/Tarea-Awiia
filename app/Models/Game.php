<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'release_date',
        'developer',
        'publisher',
        'rating',
        'is_available',
        'game_category_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'release_date' => 'date',
        'rating' => 'integer',
        'is_available' => 'boolean',
    ];

    /**
     * Obtiene la categoría a la que pertenece este juego.
     * Relación: Un juego pertenece a una categoría (Many-to-One)
     */
    public function gameCategory(): BelongsTo
    {
        return $this->belongsTo(GameCategory::class);
    }
}
