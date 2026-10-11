# Plataforma BL — Trade & Business SAS

Contenido de este paquete (para meter dentro de un proyecto Laravel nuevo,
creado con `composer create-project laravel/laravel bl-platform`):

```
app/Models/Port.php                          # puertos + numeración atómica del BL
app/Models/BillOfLading.php                  # modelo principal + emitir()/anular()
app/Models/BlCargoItem.php                   # líneas de mercancía
database/migrations/*.php                    # 3 migraciones (ports, bills_of_lading, bl_cargo_items)
database/seeders/PortSeeder.php              # carga BVT, CTG, BAQ, SMR, TBO
database/seeders/RolePermissionSeeder.php    # roles: gerencia, coordinacion, auxiliar
resources/views/pdf/bill-of-lading.blade.php # plantilla del formato, para Browsershot
public/images/logo-trade-business.png        # logo oficial
Dockerfile                                   # imagen de la app (PHP 8.3 + extensiones)
docker-compose.yml                           # app + postgres, red y volumen propios
.dockerignore
.env.example                                 # copiar a .env y ajustar
```

## Pasos para levantarlo

1. Crea el proyecto Laravel base: `composer create-project laravel/laravel bl-platform`
2. Copia todo el contenido de este paquete DENTRO de esa carpeta (respeta las rutas).
3. Instala los paquetes: `composer require livewire/livewire spatie/laravel-permission spatie/browsershot`
4. En `database/seeders/DatabaseSeeder.php`, agrega dentro de `run()`:
   `$this->call([PortSeeder::class, RolePermissionSeeder::class]);`
5. En `app/Models/User.php`, agrega el trait:
   `use Spatie\Permission\Traits\HasRoles;`
6. Copia `.env.example` a `.env` y ajusta `DB_PASSWORD` (debe coincidir con `docker-compose.yml`).
7. `docker compose up -d --build`
8. `docker compose exec app php artisan key:generate`
9. `docker compose exec app php artisan migrate --seed`
10. Entra a `http://localhost:8090`

Puertos de host usados (ajustables en `docker-compose.yml` si chocan con otro proyecto):
- App: `8090`
- Postgres: `5433` (el 5432 interno del contenedor no se toca)
