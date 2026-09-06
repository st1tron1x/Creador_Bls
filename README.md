# Plataforma BL — Trade & Business SAS

Plataforma web para generar, buscar y gestionar Bills of Lading (BL),
reemplazando el formato preimpreso membreteado. Construida en Laravel 13 +
Livewire, corriendo en Docker (PHP 8.4, PostgreSQL, Chromium para PDF).

## Usuarios de prueba

Todos con contraseña: **`password`**

| Rol | Email | Puede |
|---|---|---|
| **admin** | `test@example.com` | Todo — incluida gestión de usuarios (pendiente de UI) y aprobar anulaciones sin necesitar a nadie más |
| **gerencia** | `gerencia@test.com` | Crear/emitir BL, aprobar anulaciones, ver reportes (pendiente de UI) |
| **coordinacion** | `coordinador@test.com` | Crear/emitir BL, aprobar anulaciones (de otros, no las propias) |
| **auxiliar** | `auxiliar@test.com` | Crear/emitir BL, solicitar anulación (no puede aprobar ni anular directamente) |

Se crean automáticamente al correr `php artisan migrate:fresh --seed`.

## Rutas principales

| Ruta | Qué hace |
|---|---|
| `/login` | Inicio de sesión (Breeze + Livewire) |
| `/bl` | Listado + búsqueda de BL, con filtros |
| `/bl/create` | Wizard de 5 pasos para crear un BL |
| `/bl/create/{id}` | Continuar editando un BL en borrador |
| `/bl/{id}` | Detalle de un BL: datos, líneas de carga, PDF, anulación, auditoría |
| `/bl/{id}/pdf` | Ver el PDF (en pestaña nueva) |
| `/bl/{id}/pdf?download=1` | Descargar el PDF |
| `/dev/bl-preview/{portCode}` | ⚠️ Vista previa de la plantilla sin login — **eliminar antes de producción** |

## Flujo de anulación

1. Cualquier rol (incluido auxiliar) puede **solicitar** la anulación de un BL emitido, con motivo.
2. Queda en estado "Anulación solicitada" hasta que alguien de **rango igual o superior, distinto a quien solicitó**, la apruebe o rechace.
3. Excepción: si quien solicita ya es `admin` (el rango más alto), se autoaprueba de inmediato.
4. Un auxiliar nunca puede aprobar, sin importar quién haya solicitado.
5. Toda acción (creado, visto, descargado, solicitud, aprobación, rechazo) queda registrada en la tabla de Auditoría de cada BL.

## Pendientes conocidos (antes de considerar esto "entregado")

- [ ] Botón "Guardar borrador y salir" + navegación por clic en los pasos del wizard
- [ ] CRUD de usuarios (crear, editar, asignar rol) — hoy solo se puede por Tinker/seeder
- [ ] Mejorar visualmente el login (ya tiene logo y texto, falta pulir estilo)
- [ ] Dashboards distintos según el rol (hoy es la pantalla genérica de Breeze)
- [ ] Pantalla de reportes/estadísticas (el permiso `reports.view` existe, la pantalla no)
- [ ] Ajustar el formato visual del PDF a la réplica exacta del formato oficial (queda para el final, a la espera de respuestas de operaciones)
- [ ] Eliminar o proteger la ruta `/dev/bl-preview` antes de producción
- [ ] Despliegue real (VPS recomendado: Hetzner CPX21 — ver detalles en la conversación)

## Comandos Docker más usados

```
docker compose up -d              # levantar (tras un stop/down)
docker compose stop               # pausar sin borrar contenedores (recomendado al apagar el PC)
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=NombreDelSeeder   # correr un seeder puntual sin perder datos
docker compose logs app           # ver errores si algo no levanta
```

## Estructura del proyecto (piezas agregadas por esta plataforma)

```
app/Models/                 Port, BillOfLading, BlCargoItem, BlVoidRequest, BlAuditLog
app/Livewire/                BillOfLadingForm.php (wizard de 5 pasos)
app/Http/Controllers/        BillOfLadingController.php, DevPreviewController.php
resources/views/bl/          index.blade.php, show.blade.php
resources/views/livewire/    bill-of-lading-form.blade.php
resources/views/pdf/         bill-of-lading.blade.php (plantilla del formato)
resources/views/components/  layout.blade.php
database/migrations/         ports, bills_of_lading, bl_cargo_items, bl_void_requests, bl_audit_logs
database/seeders/            PortSeeder, RolePermissionSeeder, DatabaseSeeder
config/role_ranks.php        jerarquía de roles para la aprobación de anulaciones
```