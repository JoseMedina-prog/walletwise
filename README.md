# WalletWise

> Aplicación web de finanzas personales tipo MVP — Laravel 12 + MySQL + Tailwind CSS + Chart.js.

WalletWise permite a cada usuario registrar sus **ingresos y gastos**, organizarlos por **categorías** personalizadas, visualizar un **dashboard** con métricas y gráficos, generar **reportes filtrados** y **exportar** todas sus transacciones a **CSV**.

Multi-tenant estricto: cada usuario solo ve, edita y elimina **sus propios** datos.

---

## Capturas de pantalla (sugeridas)

| Dashboard | Transacciones | Reportes |
|---|---|---|
| `docs/screenshots/dashboard.png` | `docs/screenshots/transactions.png` | `docs/screenshots/reports.png` |
| KPIs, gráfico de barras 6 meses, dona por categoría | Tabla con filtros, badges ingreso/gasto, paginación | Breakdown por categoría con porcentajes |

| Categorías | Modo oscuro |
|---|---|
| `docs/screenshots/categories.png` | `docs/screenshots/dark-mode.png` |
| CRUD en 2 columnas (Ingresos / Gastos) | Toggle dark/light en navbar superior |

> Para tomarlas: `php artisan serve`, navega cada pantalla y captura con tu herramienta preferida.

---

## Tech stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade + Tailwind CSS 4 (Vite)
- **Base de datos:** MySQL 8
- **Autenticación:** Laravel Breeze (Blade stack)
- **Gráficos:** [Chart.js 4](https://www.chartjs.org/) vía CDN
- **Testing:** PHPUnit 11 (25 tests pasan)
- **ORM:** Eloquent
- **Sin paquetes adicionales** — todo construido con lo que trae Laravel + Breeze.

---

## Características

- Registro e inicio de sesión (multi-usuario)
- CRUD de **Categorías** (ingresos y gastos)
- CRUD de **Transacciones** con importe, fecha, descripción y categoría
- **Dashboard** con:
  - Balance total histórico
  - Ingresos / gastos del mes actual
  - Tasa de ahorro del mes
  - Gráfico de barras: ingresos vs gastos (últimos 6 meses)
  - Gráfico de dona: gastos por categoría (mes actual)
  - Tabla de resumen mensual
  - Movimientos recientes
- **Reportes** filtrados por rango de fecha, tipo y categoría
  - KPIs del período
  - Breakdown por categoría con % del total
  - Detalle de las primeras 50 transacciones
- **Filtros** en la lista de transacciones (desde, hasta, tipo, categoría)
- **Exportación CSV** (lista de transacciones y reportes)
- Soporte **dark / light mode**
- Responsive
- Multi-tenant: cada usuario solo accede a sus propios datos (validado con `auth()->id()` en cada query)

---

## Requisitos

- PHP **8.2+** con extensiones: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`, `zip`, `intl`, `fileinfo`
- Composer 2
- Node.js 18+ y npm
- MySQL 8 (o MariaDB equivalente)
- Git

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/walletwise.git
cd walletwise

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Crear el archivo .env desde el ejemplo
cp .env.example .env

# 5. Generar la clave de aplicación
php artisan key:generate

# 6. Crear la base de datos MySQL
#    Conectate a MySQL y ejecuta:
#    CREATE DATABASE walletwise_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 7. Editar .env con tus credenciales de MySQL
#    DB_DATABASE=walletwise_db
#    DB_USERNAME=root
#    DB_PASSWORD=tu_password

# 8. Ejecutar migraciones
php artisan migrate

# 9. (Opcional) Cargar datos de prueba
php artisan db:seed
# Crea el usuario demo@walletwise.test / password
# con 11 categorías y 20 transacciones de los últimos 60 días

# 10. Compilar assets
npm run build

# 11. Levantar el servidor
php artisan serve
# → http://localhost:8000
```

### Modo desarrollo (hot reload)

```bash
# Terminal 1: backend
php artisan serve

# Terminal 2: Vite (recarga automática)
npm run dev
```

---

## Credenciales de prueba

Si ejecutaste `php artisan db:seed`:

| Email | Password |
|---|---|
| `demo@walletwise.test` | `password` |

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              (Breeze: login, register, password reset...)
│   │   ├── ProfileController.php
│   │   ├── DashboardController.php
│   │   ├── CategoryController.php        (resource)
│   │   ├── TransactionController.php     (resource + filtros)
│   │   ├── ReportController.php
│   │   └── ExportController.php          (CSV streaming)
│   └── Requests/
│       ├── CategoryRequest.php           (Form Request)
│       └── TransactionRequest.php        (Form Request)
└── Models/
    ├── User.php
    ├── Category.php
    └── Transaction.php

database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_categories_table.php
│   └── ..._create_transactions_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── CategorySeeder.php
    └── TransactionSeeder.php

resources/
└── views/
    ├── auth/                (Breeze auth views)
    ├── categories/          (index, create, edit, _form)
    ├── transactions/        (index, create, edit, _form)
    ├── reports/             (index)
    ├── dashboard.blade.php
    ├── layouts/             (app, navigation)
    └── components/          (Breeze Blade components)

routes/
├── web.php
└── auth.php
```

---

## Esquema de base de datos

```sql
users
  id, name, email, email_verified_at, password, remember_token, timestamps

categories
  id, user_id (FK→users), name, type (enum: income|expense), timestamps
  INDEX (user_id, type)

transactions
  id, user_id (FK→users), category_id (FK→categories), type (enum: income|expense),
  amount (decimal 12,2), description (nullable), transaction_date (date), timestamps
  INDEX (user_id, transaction_date)
  INDEX (user_id, type)
```

### Diagrama de relaciones

```
User (1) ──< (N) Category (1) ──< (N) Transaction >── (1) User
```

---

## Rutas

| Método | URI | Nombre | Descripción |
|---|---|---|---|
| GET | `/register` | `register` | Registro de usuario |
| GET | `/login` | `login` | Inicio de sesión |
| GET | `/dashboard` | `dashboard` | Dashboard principal (requiere auth) |
| GET | `/categories` | `categories.index` | Listado de categorías |
| POST | `/categories` | `categories.store` | Crear categoría |
| GET | `/categories/create` | `categories.create` | Formulario nueva |
| GET | `/categories/{id}/edit` | `categories.edit` | Formulario edición |
| PUT | `/categories/{id}` | `categories.update` | Actualizar |
| DELETE | `/categories/{id}` | `categories.destroy` | Eliminar (si no tiene tx) |
| GET | `/transactions` | `transactions.index` | Listado + filtros |
| POST | `/transactions` | `transactions.store` | Crear transacción |
| GET | `/transactions/create` | `transactions.create` | Formulario |
| GET | `/transactions/{id}/edit` | `transactions.edit` | Formulario edición |
| PUT | `/transactions/{id}` | `transactions.update` | Actualizar |
| DELETE | `/transactions/{id}` | `transactions.destroy` | Eliminar |
| GET | `/reports` | `reports.index` | Reportes con filtros |
| GET | `/exports/transactions.csv` | `exports.transactions` | Descargar CSV |

---

## Multi-tenant / Seguridad

Todas las queries filtran por `auth()->id()`:

- **Categorías**: `Category::where('user_id', auth()->id())`
- **Transacciones**: `Transaction::where('user_id', auth()->id())`
- **Route Model Binding + `abort_if`**: en `edit`, `update`, `destroy` se valida `user_id` para devolver 403 si pertenece a otro usuario.
- **Validación de Form Request**: las categorías de un usuario no pueden asignarse a transacciones de otro (`Rule::exists` con scope por user).
- **CSRF**: activo en todos los formularios (Breeze default).
- **Auth middleware**: protege todas las rutas excepto `/`, `/register`, `/login`, `/forgot-password`, `/reset-password/*`.

---

## Testing

```bash
php artisan test
```

25 tests pasan (los que vienen con Breeze + Laravel 12). Para agregar tests propios:

```bash
php artisan make:test CategoryTest
php artisan make:test TransactionTest
```

---

## Formateo de código

```bash
./vendor/bin/pint
```

Usa [Laravel Pint](https://laravel.com/docs/pint) (basado en PHP-CS-Fixer) con la configuración por defecto de Laravel.

---

## Roadmap

### ✅ Implementado en oleadas anteriores

Funcionalidades ya disponibles en esta versión:

**Gestión y operación**
- [x] **Docker + docker-compose** para setup local con PHP 8.3, Nginx, MySQL 8.4, Redis
- [x] **Búsqueda full-text** por descripción en transacciones (preserva filtros de fecha/tipo/categoría)
- [x] **Presupuestos mensuales** por categoría con alertas (umbral `warn`/`over`) y widget en dashboard
- [x] **Notificaciones in-app** (campana con badge) disparadas por presupuestos excedidos, con deduplicación por mes
- [x] **Suscripciones recurrentes** (sueldos, alquileres, suscripciones) con posting manual y cálculo de próxima fecha
- [x] **Metas de ahorro** con seguimiento de progreso, aporte mensual sugerido, proyección y estado on-track
- [x] **Comparación mes a mes** en KPIs del dashboard (delta % vs mes anterior)
- [x] **Tests Feature completos** para Transactions, Reports, Budgets, Goals, Recurrings, Notifications y Profile (153 tests, 366 assertions)

**Operaciones internas**
- [x] **CI** con GitHub Actions
- [x] **Privacy-first** del admin: nunca accede a datos financieros de usuarios

### 🔴 Pendiente — siguiente oleada (alta prioridad)

Funcionalidades con buena relación valor/esfuerzo. Cada una abre una categoría de features para una futura oleada 3:

- [ ] **Importación CSV** — camino inverso al export con preview y mapeo de categorías
- [ ] **Adjuntar comprobantes** (imágenes/PDF) a transacciones con storage privado y autorización por Policy
- [ ] **Tags / etiquetas** — segunda dimensión de clasificación complementaria a categoría
- [ ] **Heatmap calendario** mensual de gastos (engagement visual, retención diaria)
- [ ] **Auditoría / activity log** de cambios en transacciones, presupuestos y categorías (confianza + debugging)
- [ ] **Exportación a PDF** del reporte mensual (DomPDF o HTML imprimible)
- [ ] **Notificaciones por email** de resúmenes semanales y alertas de presupuesto

### 🟡 Diferenciadores de producto (media prioridad)

- [ ] **Multi-cuenta / wallets** (efectivo, banco, tarjeta) con transferencias internas
- [ ] **Reglas de categorización automática** (regex sobre descripción) por usuario
- [ ] **Dashboard comparativo** con períodos arbitrarios (no solo mes a mes)
- [ ] **Filtros guardados** por usuario con nombre y combinación rápida
- [ ] **Cron automático para recurrentes** (`walletwise:recurring:post` + `Schedule::daily()`)
- [ ] **Gráfico de tendencia** del balance acumulado en el tiempo (línea, no barras)

### 🟢 Features avanzadas (largo plazo)

Requieren dependencias externas, decisiones de arquitectura o trabajo adicional significativo:

- [ ] **API REST + Sanctum** para app móvil nativa o integraciones de terceros
- [ ] **Open Banking** (Plaid US/CA, TrueLayer EU, Belvo LATAM) para importar transacciones automáticamente
- [ ] **Multi-moneda** con tasas de cambio y conversión en tiempo real
- [ ] **OCR de tickets** (Google Vision / AWS Textract / Veryfi) con extracción de comercio, importe y fecha
- [ ] **Coach financiero con IA** ("gastaste 23% más en restaurantes este mes") con OpenAI/Anthropic o heurísticas locales
- [ ] **PWA offline** con Service Worker + IndexedDB para registrar gastos sin conexión

### 🛠️ Calidad y DX

- [ ] **Internacionalización (i18n)** — mover strings hardcoded a `lang/es.json` y soporte multi-idioma
- [ ] **Autenticación de dos factores** (Laravel Fortify + Google2FA)
- [ ] **Roles y permisos granulares** (Spatie Laravel-Permission) si el proyecto sale del MVP de admin único
- [ ] **Modo invitado / demo** sin necesidad de registro (landing con datos de muestra)
- [ ] **Pint + PHPStan** integrados en CI con umbrales de calidad

---

## Licencia

[MIT](https://opensource.org/licenses/MIT) — uso libre para proyectos personales y comerciales.

---

## Agradecimientos

- [Laravel](https://laravel.com)
- [Breeze](https://laravel.com/docs/starter-kits#laravel-breeze)
- [Tailwind CSS](https://tailwindcss.com)
- [Chart.js](https://www.chartjs.org)
