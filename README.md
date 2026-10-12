# Plataforma BL — Creador de Bills of Lading (multiempresa)

Aplicación web interna para **crear, emitir, consultar, descargar en PDF y anular Bills of Lading (BL)** de dos empresas que comparten usuarios:

| Empresa | Numeración | Formato PDF |
|---|---|---|
| **Trade & Business S.A.S.** | Por puerto: `BVT-0007`, `BAQ-0004`… (un consecutivo independiente por puerto) | `trade` — hoja 215.14 × 280 mm, marca de agua "ORIGINAL", cajas redondeadas, logo y código en rojo |
| **Global Trade Business Corp US** | Un solo consecutivo general de la empresa: `FMC OTI NO 0001`, `FMC OTI NO 0002`… (sin puertos) | `global` — hoja A4, formato HBL estilo ATO |

Después de iniciar sesión, el usuario elige con cuál empresa va a trabajar; a partir de ahí todo (listado, dashboard, formulario, numeración y PDF) es de esa empresa.

> Manual para el usuario final (pantalla por pantalla, campo por campo, según rol): ver [`MANUAL_USUARIO.md`](MANUAL_USUARIO.md).

---

## 1. Características

- **Multiempresa**: selección de empresa tras el login, cambio de empresa desde el menú del usuario, datos aislados por empresa (un usuario no puede abrir un BL de una empresa a la que no pertenece: responde 403).
- **Asistente (wizard) de 5 pasos** por empresa, con **guardado automático de borrador** al avanzar, navegación por pasos y "Guardar borrador y salir".
- **Numeración atómica** (con bloqueo de fila en PostgreSQL): dos usuarios emitiendo al mismo tiempo nunca obtienen el mismo número.
- **PDF con Browsershot/Chromium**, un formato distinto por empresa; el formato Trade pagina solo (hoja de continuación) cuando la mercancía no cabe en una hoja.
- **Estados del BL**: `borrador → emitido → anulacion_solicitada → anulado` (o vuelve a `emitido` si se rechaza la anulación).
- **Flujo de anulación con jerarquía de roles** (solicitud + aprobación/rechazo; el admin se autoaprueba).
- **Notificaciones internas** (solicitud enviada / resuelta) y **bitácora de auditoría** por BL (creó, editó, consultó, descargó, solicitó/aprobó/rechazó anulación).
- **Dashboard** con contadores, BL recientes, pendientes de aprobar, top shippers/consignees, tendencia mensual, promedio de horas de creación→emisión y borradores antiguos.
- **Listado con filtros**: número de BL, shipper, consignee, puerto, estado y rango de fechas.
- **Gestión de usuarios** (crear, editar, rol, activar/desactivar, eliminar si no tiene BL asociados).
- **Roles y permisos** con Spatie Permission: `admin`, `gerencia`, `coordinacion`, `auxiliar`.

## 2. Tecnología

| Capa | Tecnología |
|---|---|
| Lenguaje / framework | PHP 8.4 (mínimo 8.3), Laravel 13.x |
| Interfaz | Blade + Livewire 3 (wizard) + Alpine.js (incluido en Livewire) + Volt/Breeze (login y perfil) |
| Base de datos | PostgreSQL 16 |
| Roles y permisos | spatie/laravel-permission 8 |
| PDF | spatie/browsershot 5 + Chromium (HTML → PDF) |
| Contenedores | Docker Compose: `app` (php artisan serve) + `db` (postgres:16-alpine) |
| Sesiones / caché / colas | Driver `database` |
| Front build | Vite + Tailwind (assets en `public/build`) |

## 3. Cómo funciona (arquitectura)

### 3.1 Empresas y numeración

Tabla `companies` (una fila por empresa) con estas columnas clave:

- `numbering_scheme`: `port_based` (Trade & Business) o `company_based` (Global).
- `fixed_prefix`: texto fijo del consecutivo cuando es `company_based` (Global = `FMC OTI NO`).
- `pdf_template`: `trade` o `global`; decide qué plantilla Blade se usa para el PDF.
- `logo_path`, `address`, `phone`, `email`: datos del membrete (se leen de aquí; si están vacíos el PDF muestra el espacio vacío o un texto de relleno).

Los consecutivos viven en `bl_sequences` (`company_id`, `label`, `last_number`; único por `company_id + label`):
- Trade & Business: `label` = código del puerto (BVT, CTG, BAQ, SMR, TBO).
- Global: `label` = `FMC OTI NO`.

Al **emitir** (`BillOfLading::issue()`), dentro de una transacción: se bloquea la empresa, se calcula el `label`, `Company::reserveNextSequenceFor($label)` incrementa el contador con `lockForUpdate`, y `Company::formatBlNumber()` arma el número (separador `-` en port_based, espacio en company_based, relleno a 4 dígitos). Mientras el BL es borrador **no tiene número**.

### 3.2 Flujo de sesión y empresa

`login → middleware active (usuario activo) → middleware company (EnsureCompanySelected)`. Si el usuario pertenece a una sola empresa, se activa sola; si tiene varias y no hay una activa en sesión, va a `/select-company`. La empresa activa vive en `session('active_company_id')` y se obtiene con `User::activeCompany()`. Cambiar de empresa: menú del usuario → "Cambiar empresa".

### 3.3 Estados del BL

```
borrador ──emitir──▶ emitido ──solicitar anulación──▶ anulacion_solicitada ──aprobar──▶ anulado
                        ▲                                      │
                        └────────────── rechazar ──────────────┘
```
Un BL `emitido` ya no se edita. Un `anulado` queda como registro (no se reutiliza su número).

### 3.4 PDF

`BillOfLadingController@pdf` elige la plantilla según `company->pdf_template`:

- **trade** → `resources/views/pdf/bill-of-lading.blade.php`, papel `215.14 × 280 mm`, márgenes 0, sin encabezado de Browsershot. La marca de agua, el encabezado de continuación y la paginación están dentro del propio HTML (capacidad: 24 líneas en la primera hoja, 66 en las de continuación; descripción se parte a 68 caracteres y marcas a 28).
- **global** → `resources/views/pdf/bill-of-lading-global.blade.php`, A4, márgenes 0.

Ambas usan posicionamiento absoluto en milímetros: **todo elemento posicionado necesita `position:absolute`** o el formato se desordena.

Los PDFs se generan temporalmente en `storage/app/temp/` y se envían al navegador.

## 4. Estructura del proyecto (lo importante)

```
app/
  Http/Controllers/
    BillOfLadingController.php        # listado, detalle, PDF, solicitud/aprobación de anulación
    BillOfLadingCreateController.php  # elige el wizard según la empresa activa
    CompanySelectionController.php    # pantalla "¿Con cuál empresa vas a trabajar?"
    DashboardController.php           # (el archivo debe llamarse EXACTAMENTE así, con D mayúscula)
    UserController.php, NotificationController.php, ProfileController.php
  Http/Middleware/
    EnsureUserIsActive.php  (alias active)   EnsureCompanySelected.php (alias company)
  Livewire/
    BillOfLadingForm.php        # wizard Trade & Business
    GlobalBillOfLadingForm.php  # wizard Global
  Models/ BillOfLading, BlCargoItem, BlSequence, BlVoidRequest, BlAuditLog, Company, Port, User
database/migrations/            # ver sección 6
database/seeders/               # PortSeeder, RolePermissionSeeder, DatabaseSeeder
resources/views/
  components/layout.blade.php   # layout general + menú de usuario
  company/select.blade.php      # selección de empresa
  bl/ (index, show, create-wrapper)   livewire/ (formularios)   pdf/ (plantillas)
public/images/                  # logos de cada empresa
routes/web.php                  # rutas con middleware ['auth','active','company']
config/role_ranks.php           # jerarquía de roles para aprobar anulaciones
```

## 5. Modelo de datos (tablas principales)

| Tabla | Para qué |
|---|---|
| `users` (+ `is_active`, `phone`) | Usuarios |
| `roles`, `permissions`, pivotes Spatie | Roles y permisos |
| `companies`, `company_user` | Empresas y a cuáles pertenece cada usuario |
| `ports` | Catálogo de puertos (solo Trade & Business) |
| `bl_sequences` | Consecutivos por empresa y etiqueta |
| `bills_of_lading` | El BL (tabla en plural irregular: `bills_of_lading`; el modelo declara `$table`) |
| `bl_cargo_items` | Líneas de carga de cada BL |
| `bl_void_requests` | Solicitudes de anulación |
| `bl_audit_logs` | Bitácora por BL |
| `notifications`, `sessions`, `cache`, `jobs` | Infraestructura Laravel |

Campos exclusivos de Global en `bills_of_lading` (quedan NULL en Trade): `port_of_loading_text`, `domestic_routing`, `export_references`, `cargo_delivery_contact`, `forwarding_agent_references`, `exporting_carrier`, `on_carriage_by`, `net_weight`, `total_charges`. En Global `port_id` es NULL; el puerto de carga se escribe libre en `port_of_loading_text`.

## 6. Instalación local (Windows 11 + Docker Desktop)

Requisitos: Docker Desktop en marcha y Git.

```powershell
git clone https://github.com/st1tron1x/Creador_Bls.git bl-platform
cd bl-platform
copy .env.example .env
```

1. Edita `.env`: `APP_KEY` vacío (se genera abajo), `DB_PASSWORD=` **la misma clave** que `POSTGRES_PASSWORD` / `DB_PASSWORD` de `docker-compose.yml`. **Importante:** `php artisan serve` ignora las variables de entorno del contenedor y lee solo `.env`; si el password falta ahí sale `SQLSTATE[08006] no password supplied`.
2. Levanta y prepara:

```powershell
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed     # ojo: es --seed (con "e")
docker compose exec app npm install
docker compose exec app npm run build
```
3. Abre <http://localhost:8090>.

### Migraciones en orden (todas deben aparecer `Ran` en `php artisan migrate:status`)

```
0001_01_01_000000_create_users_table
0001_01_01_000001_create_cache_table
0001_01_01_000002_create_jobs_table
2026_08_23_000001_create_ports_table
2026_08_23_000002_create_bills_of_lading_table
2026_08_23_000003_create_bl_cargo_items_table
2026_08_24_002542_create_permission_tables
2026_08_25_000001_add_bill_of_loading_ref_to_bills_of_lading
2026_08_26_000001_make_sequence_and_bl_number_nullable
2026_08_27_000001_create_bl_void_requests_table
2026_08_27_000002_create_bl_audit_logs_table
2026_08_27_000003_add_pending_void_status_to_bills_of_lading
2026_08_27_000004_add_is_active_to_users_table
2026_08_28_000001_create_notifications_table
2026_08_29_000001_add_phone_to_users_table
2026_09_06_000001_create_companies_table
2026_09_06_000002_create_company_user_table
2026_09_06_000003_create_bl_sequences_table
2026_09_06_000004_add_company_id_to_bills_of_lading      # crea Trade & Business y asocia BL, consecutivos y usuarios existentes
2026_09_12_000001_add_global_fields_to_bills_of_lading
```

### Crear la empresa Global (la migración solo crea Trade & Business)

```powershell
docker compose exec app php artisan tinker
```
```php
$g = App\Models\Company::create([
  'name' => 'Global Trade Business Corp US', 'short_name' => 'Global Trade Business',
  'numbering_scheme' => 'company_based', 'fixed_prefix' => 'FMC OTI NO', 'pdf_template' => 'global',
  'logo_path' => 'images/LOGO_GLOBAL_TRADE_BUSINESS CORP.jpg',
  'address' => 'DIRECCIÓN REAL', 'phone' => 'TELÉFONO REAL', 'email' => 'CORREO REAL', 'active' => true,
]);
$g->users()->sync(App\Models\User::pluck('id'));   // todos los usuarios operan ambas empresas
```
Para corregir datos del membrete más tarde: `App\Models\Company::find(2)->update(['address'=>'…','phone'=>'…','email'=>'…']);`

### Primer usuario administrador

```php
$u = App\Models\User::factory()->create(['name'=>'Tu Nombre','email'=>'tu@correo.com','password'=>bcrypt('CLAVE-SEGURA')]);
$u->assignRole('admin');
$u->companies()->sync(App\Models\Company::pluck('id'));
```
(`DatabaseSeeder` crea usuarios de prueba con contraseña `password`: **bórralos o cámbialos antes de producción**.)

## 7. Variables de entorno (`.env`)

| Variable | Valor típico | Nota |
|---|---|---|
| `APP_ENV` / `APP_DEBUG` | `production` / `false` en producción | `true` solo en desarrollo (muestra trazas) |
| `APP_URL` | `https://tu-dominio` | |
| `DB_HOST` | `db` (Docker) / `127.0.0.1` (hosting) | |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | según tu servidor | la clave real no va a GitHub |
| `SESSION_DRIVER` | `database` | |
| `SESSION_LIFETIME` | `30` (minutos de **inactividad**; por defecto de Laravel: 120) | ver sección 9 |
| `SESSION_MAX_HOURS` | `10` (duración máxima **absoluta**; `0` = sin límite) | ver sección 9 |
| `QUEUE_CONNECTION`, `CACHE_STORE` | `database` | |

Tras cambiar `.env`: `docker compose exec app php artisan config:clear`.

## 8. Respaldo y restauración de la base de datos

```powershell
# Respaldo (NO uses ">" de PowerShell: guarda en UTF-16 y se corrompe)
docker exec bl-platform-db pg_dump -U bl_platform_user -Fc bl_platform -f /tmp/backup.dump
docker cp bl-platform-db:/tmp/backup.dump .\backup.dump

# Restauración sobre una base vacía
docker cp .\backup.dump bl-platform-db:/tmp/backup.dump
docker exec bl-platform-db pg_restore -U bl_platform_user -d bl_platform --clean --if-exists /tmp/backup.dump
```
El `.dump` contiene datos reales: **no se sube a GitHub** (guárdalo en OneDrive/disco externo). Haz respaldo antes de cualquier migración o despliegue.

## 9. Seguridad

Estado actual:

- Login con regeneración de sesión; **logout invalida la sesión y regenera el token CSRF**; usuario inactivo → se cierra su sesión.
- Permisos **comprobados en el servidor** (`abort_unless(... can(...))` en controladores y componentes Livewire), no solo ocultando botones.
- Aislamiento por empresa en listados, detalle y PDF.
- Un usuario no puede aprobar su propia solicitud, ni desactivarse o eliminarse a sí mismo.
- Registro público deshabilitado; los usuarios los crea un admin/gerencia.

Parámetros de sesión (configurables en `.env`; tras cambiarlos: `php artisan config:clear`):

| Parámetro | Recomendación | Estado |
|---|---|---|
| Inactividad máxima | 20–30 min → `SESSION_LIFETIME=30` | Implementado (configurable en `.env`) |
| Duración máxima absoluta | 8–12 h → `SESSION_MAX_HOURS=10` | Implementado (middleware `EnforceSessionLimit`) |
| Al cerrar sesión | Invalidar sesión y regenerar token CSRF | Implementado |
| Al iniciar sesión | Regenerar identificador de sesión | Implementado (Breeze) |
| Acciones sensibles | Re-comprobar permisos en servidor | Implementado en emitir/anular/usuarios |

Producción: `APP_DEBUG=false`, HTTPS, `SESSION_SECURE_COOKIE=true`, cambiar claves de ejemplo, no exponer el puerto 5433 de la base.

## 10. Despliegue en hosting (cPanel) — lista de verificación

1. PHP 8.4 con extensiones `pdo_pgsql`, `mbstring`, `xml`, `curl`, `zip` (las que ya instala el `Dockerfile` más las estándar de Laravel); Composer; Node solo para compilar (o sube `public/build` ya compilado).
2. Apunta el *document root* a `public/`. `storage` y `bootstrap/cache` con permisos de escritura.
3. `.env` de producción, `php artisan key:generate`, `php artisan migrate --force`, crear empresa Global y usuario admin (sección 6).
4. `php artisan config:cache route:cache view:cache` (y `optimize:clear` ante cualquier cambio).
5. **PDF: Browsershot necesita Chromium instalado en el servidor** (`/usr/bin/chromium` en el controlador). En hosting compartido normalmente **no existe** y no se puede instalar. Opciones: servidor VPS/cloud donde sí se pueda; o pedir al proveedor que lo instale; o mover el PDF a un servicio externo. Confirma esto **antes** de contratar/migrar.
6. SSH: si el puerto 22 rechaza la conexión, el proveedor debe habilitarlo o indicar el puerto/host (el hosting lo subcontrata un tercero).
7. Cron/colas no se usan hoy; no hace falta `queue:work`.

## 11. Problemas frecuentes y solución

| Síntoma | Causa | Solución |
|---|---|---|
| `View [company.select] not found` | El archivo está en otra carpeta | Debe estar en `resources/views/company/select.blade.php` |
| `SQLSTATE[08006] no password supplied` | `.env` sin `DB_PASSWORD` (`artisan serve` ignora el entorno del contenedor) | Poner la clave en `.env`, `config:clear`, `docker compose restart app` |
| `relation "bill_of_ladings" does not exist` | Modelo `BillOfLading` sin `$table` o es una versión vieja | `protected $table = 'bills_of_lading';` (y restaurar el modelo actual) |
| `Call to undefined relationship [voidRequests]` (u otras) | Modelo viejo sin relaciones | Restaurar el `BillOfLading.php` actual (incluye `voidRequests`, `pendingVoidRequest`, `auditLogs`, `finalizeVoid`) |
| `Attempt to read property "code" on null` | BL de Global (sin puerto) en una vista que asume puerto | Usar `$bl->port?->code ?? $bl->port_of_loading_text ?? '—'` |
| Bucle de redirecciones al entrar | Middleware de empresa apuntando a la misma URL | Usar `url.intended` en sesión (ya corregido) |
| Class `DashboardController` no encontrada solo en Linux | Archivo `Dashboardcontroller.php` (minúscula) | Renombrar a `DashboardController.php` (Windows no distingue mayúsculas, Linux sí) |
| PDF desordenado | Faltó `position:absolute` en una clase de la plantilla | Todas las cajas posicionadas deben tenerlo |
| El número sale `FMC OTI NOFMC OTI NO 0001` | La plantilla repetía el prefijo | Mostrar solo `$bl_number` (ya corregido) |
| Número con guion `FMC OTI NO. -0001` | BL creado antes del cambio de formato | Es dato viejo; anular/eliminar el BL de prueba |
| Membrete con `(tu direccion)` | `companies.address/phone/email` son texto de relleno | Actualizar con tinker (sección 6) |
| "Solicitar anulación" parpadea y no pasa nada | El motivo tiene **menos de 5 caracteres** (validación) | Escribir un motivo de al menos 5 caracteres |
| `migrate --sed` no hace nada | Opción mal escrita | `--seed` |
| `migrate --seed` falla por email duplicado | `DatabaseSeeder` vuelve a crear usuarios de prueba | Correr solo `php artisan db:seed --class=PortSeeder` / el seeder necesario |
| Respaldo `.sql` ilegible | `pg_dump > archivo` en PowerShell (UTF-16) | Usar `docker exec … -f /tmp/backup.dump` + `docker cp` |
| Puerto 8090/5433 ocupado | Otra app local | Cambiar el puerto del lado izquierdo en `docker-compose.yml` |
| Cambios no se reflejan | Caché de config/vistas | `php artisan optimize:clear` |

Logs: `storage/logs/laravel.log` (`docker compose logs -f app` para el servidor).

## 12. Limitaciones conocidas y mejoras propuestas

Limitaciones actuales (comprobadas en el código):

- **Global — "Total Number of Containers or Packages (in words)"**: la plantilla tiene el espacio pero el sistema **no envía ese dato**, queda vacío. Propuesta: campo de texto en el wizard (p. ej. `FIVE (5) CONTAINERS ONLY`).
- **Trade — fila `TOTAL`** de la tabla de carga: no suma automáticamente.
- **Global — columnas de carga independientes**: cada columna apila sus textos; si la descripción tiene más líneas que las marcas, las filas pueden quedar desalineadas (el formato Trade sí alinea por línea).
- `RolePermissionSeeder.php` del repositorio puede estar desactualizado frente a la base real (permisos `bl.void.request`, `bl.void.approve` y rol `admin`); ver el archivo incluido en el parche y verificar con `Spatie\Permission\Models\Permission::pluck('name')`.
- El filtro "Puerto" del listado aparece también para Global (donde no aplica).
- Limpieza recomendada del repo: `show.blade copy.php`, `bill-of-lading.blade_bk.php`, `resources/components/dashboard.blade.php` (duplicado), `backup.dump`, `backup.sql`, `database/database.sqlite`, y los PDFs de `storage/app/temp/` (añadir a `.gitignore`).

Mejoras propuestas (aún no implementadas):

1. **Catálogo mundial de puertos con escritura libre** para puerto de carga/descarga, origen y destino: buscador que permita elegir del catálogo o escribir un valor manual.
3. Campo "total en letras" automático y suma de la fila TOTAL.

### Funciones recientes

- **Duplicar BL** (botón "Duplicar como borrador" en el detalle de cualquier BL de la empresa activa; requiere permiso `bl.create`): crea un **nuevo borrador** con shipper, consignee, notify, transporte, carga y flete. **No copia** número/consecutivo (se asigna al emitir), estado, anulaciones, auditoría, fecha de embarque, referencia de la naviera ni referencia del shipper. Queda registrado en la auditoría ("Duplicado a partir del BL …"). Solo se duplica dentro de la misma empresa.
- **Sesión**: cierre por inactividad (`SESSION_LIFETIME`) y por duración máxima absoluta (`SESSION_MAX_HOURS`, vía `EnforceSessionLimit`, registrado en el grupo `web` en `bootstrap/app.php`). Al vencer se cierra la sesión, se invalida el token CSRF y se envía al login con un aviso; en pantallas Livewire el navegador recibe 419 y recarga hacia el login. Para que el límite empiece a contar desde el login, el contador se crea en la primera petición autenticada.

## 13. Flujo de trabajo con Git (para no volver a perder trabajo)

```powershell
git status
git add -A
git commit -m "Descripción del cambio"
git push
```
Haz commit y push **al terminar cada sesión de trabajo**. Nunca subas `.env`, `backup.dump`, `vendor/` ni `node_modules/` (revisa `.gitignore`).

---
Mantenimiento: TyC / Trade & Business — Coordinación TI.
