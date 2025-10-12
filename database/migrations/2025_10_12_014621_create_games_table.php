<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título del juego
            $table->text('description')->nullable(); // Descripción del juego
            $table->decimal('price', 8, 2); // Precio (ej: 59.99)
            $table->date('release_date')->nullable(); // Fecha de lanzamiento
            $table->string('developer')->nullable(); // Desarrollador
            $table->string('publisher')->nullable(); // Publicador
            $table->integer('rating')->nullable(); // Calificación (ej: de 1 a 10)
            $table->boolean('is_available')->default(true); // Si está disponible
            
            // FOREIGN KEY: Relación con game_categories
            $table->foreignId('game_category_id')
                  ->constrained('game_categories') // Se relaciona con la tabla game_categories
                  ->onDelete('cascade'); // Si se borra la categoría, se borran sus juegos
            
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
