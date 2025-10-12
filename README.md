# API REST - Gestión de Juegos y Categorías
## Práctica Universitaria - Laravel

**Estudiante:** Isaac Zamora  
**Fecha:** 11 Octubre 2025  
**Framework:** Laravel 11  
**Testing:** Pest PHP

---

## 📋 Descripción del Proyecto

API RESTful que implementa un sistema CRUD para la gestión de juegos y sus categorías.

### Relación de Modelos
- **GameCategory** (1) → (N) **Game**
- Una categoría puede tener múltiples juegos
- Cada juego pertenece a una categoría

---

## 🏗️ Proceso de Implementación

### 1. Creación de Modelos y Migraciones

```bash
# Crear modelo y migración para GameCategory
php artisan make:model GameCategory -m

# Crear modelo y migración para Game
php artisan make:model Game -m
```

**Configuración de migraciones:**
- `game_categories`: name, description, is_active
- `games`: title, description, price, release_date, developer, publisher, rating, is_available, game_category_id (FK)
- Se configuró `onDelete('cascade')` para eliminar juegos al eliminar su categoría

### 2. Configuración de Modelos Eloquent

Se implementaron en ambos modelos:
- `$fillable`: Campos permitidos para asignación masiva
- `$casts`: Conversión automática de tipos de datos
- Métodos de relación: `hasMany()` y `belongsTo()`

### 3. Creación de Controladores API

```bash
# Crear controladores con métodos API
php artisan make:controller Api/GameCategoryController --api
php artisan make:controller Api/GameController --api
```

**Funcionalidades implementadas:**
- CRUD completo (Create, Read, Update, Delete)
- Validación de datos de entrada
- Manejo de errores con try-catch
- Respuestas JSON estructuradas
- Códigos HTTP apropiados (200, 201, 404, 422, 500)

### 4. Configuración de Rutas

**Archivo:** `routes/api.php`
```php
Route::apiResource('game-categories', GameCategoryController::class);
Route::apiResource('games', GameController::class);
```

**Rutas generadas automáticamente:**
```bash
GET    /api/game-categories       # Listar todas
POST   /api/game-categories       # Crear nueva
GET    /api/game-categories/{id}  # Ver una
PUT    /api/game-categories/{id}  # Actualizar
DELETE /api/game-categories/{id}  # Eliminar
```

*(Mismas rutas para `/api/games`)*

### 5. Configuración de Factories

Se crearon factories para generar datos de prueba:
- `GameCategoryFactory`: Genera categorías con nombres realistas
- `GameFactory`: Genera juegos con relación automática a categorías

### 6. Implementación de Pruebas con Pest

**Archivo de configuración:** `.env.testing`
- Base de datos: SQLite en memoria (`:memory:`)
- Entorno aislado de desarrollo

**Cobertura de pruebas:**

#### GameCategory (12 pruebas)
- Listar todas las categorías con juegos relacionados
- Crear categoría con validaciones
- Ver categoría específica
- Actualizar categoría
- Eliminar categoría
- Validaciones: nombre requerido, nombre único
- Respuestas 404 para recursos inexistentes
- Cascade delete de juegos al eliminar categoría

#### Game (18 pruebas)
- Listar todos los juegos con categorías relacionadas
- Crear juego con validaciones completas
- Ver juego específico
- Actualizar juego (completo y parcial)
- Eliminar juego
- Validaciones:
  - Título requerido
  - Precio requerido (rango: 0-999999.99)
  - Categoría requerida y existente
  - Rating opcional (rango: 1-10)
- Cambiar categoría de un juego
- Relaciones many-to-one correctas

---

## 🧪 Ejecución de Tests

### Comandos de Testing

```bash
# Ejecutar TODAS las pruebas
php artisan test

# Ejecutar solo pruebas de GameCategory
php artisan test --filter=GameCategory

# Ejecutar solo pruebas de Game
php artisan test --filter=GameTest

# Ver detalles de cada prueba
php artisan test --verbose

# Ver cobertura (con información adicional)
php artisan test --coverage
```

### Resultado Esperado

```
PASS  Tests\Feature\GameCategoryTest
✓ puede listar todas las categorías
✓ lista de categorías incluye juegos relacionados
✓ puede crear una nueva categoría
✓ falla al crear categoría sin nombre
✓ falla al crear categoría con nombre duplicado
✓ puede ver una categoría específica
✓ retorna 404 al buscar categoría inexistente
✓ puede actualizar una categoría
✓ falla al actualizar con nombre duplicado
✓ puede eliminar una categoría
✓ retorna 404 al eliminar categoría inexistente
✓ al eliminar categoría se eliminan sus juegos (cascade)

PASS  Tests\Feature\GameTest
✓ puede listar todos los juegos
✓ lista de juegos incluye categoría relacionada
✓ puede crear un nuevo juego
✓ falla al crear juego sin título
✓ falla al crear juego sin precio
✓ falla al crear juego sin categoría
✓ falla al crear juego con categoría inexistente
✓ falla al crear juego con precio negativo
✓ falla al crear juego con rating fuera de rango
✓ puede ver un juego específico
✓ retorna 404 al buscar juego inexistente
✓ puede actualizar un juego
✓ puede actualizar solo algunos campos del juego
✓ puede cambiar la categoría de un juego
✓ puede eliminar un juego
✓ retorna 404 al eliminar juego inexistente
✓ juego mantiene relación con su categoría
✓ múltiples juegos pueden pertenecer a la misma categoría

Tests:  30 passed (192 assertions)
Duration: 0.45s
```
