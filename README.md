# EJJ - Sistema Web de Gestión de Ventas e Inventario para Ferreterías

Sistema web desarrollado en Laravel 13 para la gestión comercial y logística de la ferretería **EJJ**. Cubre el catálogo público, autenticación por roles, punto de venta, cotizaciones, órdenes de venta, pagos, comprobantes, ventas mayoristas, campañas, inventario físico, auditorías, reposición y órdenes de compra.

## Stack

- PHP 8.3+ / Laravel 13
- MySQL / MariaDB
- Blade + Tailwind CSS 4 + Vite
- Laravel DomPDF para cotizaciones, comprobantes y reportes
- PHPUnit para pruebas automatizadas

## Módulos y funcionalidades

### Acceso y seguridad
- Registro e inicio de sesión de clientes.
- Roles: `administrador`, `vendedor`, `almacenero`, `cliente`.
- Middleware de roles y protección de rutas.
- Perfil de usuario y cambio de contraseña.

### Catálogo público
- Página de inicio con categorías y productos destacados.
- Catálogo con búsqueda, filtro por categoría, disponibilidad y ordenamiento.
- Detalle de producto con stock, marca, proveedor y productos relacionados.

### Administración (CRUD)
- Productos, categorías, clientes, proveedores y usuarios.
- Eliminación lógica de productos y restauración.
- Búsqueda, filtros, paginación y validaciones.

### Punto de venta y ventas
- Carrito con búsqueda de productos, control de stock y cliente rápido.
- Cálculo automático de subtotal, descuento por volumen, descuento adicional, envío e IGV (18%).
- Cotizaciones: emisión, aceptación/rechazo, PDF y conversión a orden de venta.
- Solicitudes de descuento mayorista con evaluación del administrador.
- Órdenes de venta: estados, historial, pago, comprobante y confirmación de entrega.
- Comprobantes: boleta (DNI, serie `B001`) y factura (RUC, serie `F001`), visualización y PDF.

### Inventario y abastecimiento
- Conteo físico con comparación contra stock del sistema.
- Generación automática de auditorías y alertas por desbalance.
- Resolución de auditorías con ajuste de stock.
- Reporte de reposición de productos bajo stock mínimo y PDF.
- Órdenes de compra a proveedores y recepción con actualización de stock y costo.
- Alertas de stock bajo, desbalance y compras pendientes.

### Comunicación
- Campañas de correo a clientes activos con correo registrado (el correo se registra en el log en desarrollo).

## Requisitos

- PHP 8.3 o superior con extensiones `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` y `zip`.
- Composer.
- Node.js 20+ y npm.
- MySQL 8 o MariaDB 10.4+.

## Instalación

1. Clonar el repositorio e ingresar al proyecto:

```bash
git clone https://github.com/Jeff-amaro/Ferreteria.git
cd Ferreteria
```

2. Instalar dependencias:

```bash
composer install
npm install
```

3. Crear el archivo de entorno y la clave de aplicación:

```bash
copy .env.example .env
php artisan key:generate
```

4. Crear la base de datos:

```sql
CREATE DATABASE ferreteria_ejj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. Configurar la conexión en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ferreteria_ejj
DB_USERNAME=root
DB_PASSWORD=
```

6. Ejecutar migraciones y datos de demostración:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

7. Compilar assets:

```bash
npm run build
```

8. Iniciar el servidor:

```bash
php artisan serve
```

Abrir `http://127.0.0.1:8000`.

## Usuarios de demostración

Todos usan la contraseña `password`.

| Rol | Correo |
| --- | --- |
| Administrador | `admin@ejj.pe` |
| Vendedor | `vendedor@ejj.pe` |
| Almacenero | `almacen@ejj.pe` |
| Cliente | `cliente@ejj.pe` |

## Reglas de negocio

- **IGV:** 18% calculado sobre el subtotal menos descuentos.
- **Total:** `subtotal - descuento + envío + IGV`.
- **Descuento mayorista por volumen:** 5% desde 12 unidades, 10% desde 50 y 15% desde 100.
- **Stock:** se descuenta al convertir una cotización en orden; se restaura al anular una orden pendiente o en preparación.
- **Estados de orden:** `pendiente` → `en_preparacion` → `despachado` → `entregado`; `anulado` desde `pendiente` o `en_preparacion`.
- **Comprobantes:** DNI genera boleta `B001`; RUC genera factura `F001`.
- **Conteo físico:** las diferencias generan una auditoría y una alerta; al resolver la auditoría se ajusta el stock al conteo físico.

## Rutas principales

| Módulo | Ruta |
| --- | --- |
| Inicio | `/` |
| Catálogo | `/catalogo` |
| Panel | `/dashboard` |
| Punto de venta | `/pos` |
| Cotizaciones | `/cotizaciones` |
| Órdenes de venta | `/ordenes` |
| Comprobantes | `/comprobantes/{id}` |
| Mayoristas | `/solicitudes-mayoristas` |
| Campañas | `/campanas` |
| Inventario | `/inventario` |
| Auditorías | `/auditorias` |
| Reposición | `/reposicion` |
| Compras | `/compras` |
| Alertas | `/alertas` |
| CRUD productos | `/products` |
| CRUD clientes | `/clients` |
| CRUD proveedores | `/suppliers` |
| CRUD usuarios | `/users` |

## Pruebas

Las pruebas usan una base de datos MySQL independiente para no afectar los datos de desarrollo:

```sql
CREATE DATABASE ferreteria_ejj_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

La configuración está en `phpunit.xml`. Ejecutar:

```bash
php artisan test
```

También se puede validar el estilo del código con Laravel Pint:

```bash
vendor/bin/pint --test
```

## Estructura relevante

```text
app/
  Http/Controllers/     Controladores de CRUD, ventas, inventario y catálogo
  Http/Requests/        Validaciones de formularios
  Models/               Modelos Eloquent
  Services/             Reglas de negocio (precios, stock, códigos, comprobantes)
database/
  migrations/           Esquema de base de datos
  seeders/              Datos de demostración
resources/
  css/                  Tailwind CSS
  js/                   JavaScript de la aplicación
  views/                Vistas Blade (panel, catálogo, PDF)
routes/
  web.php               Rutas de la aplicación
```

## Notas

- El correo en desarrollo usa `MAIL_MAILER=log`; los mensajes se guardan en `storage/logs/laravel.log`.
- Los assets compilados (`public/build`) no se versionan. Ejecutar `npm run build` después de instalar o modificar estilos.
- Los respaldos locales de base de datos en `storage/backups` están excluidos del repositorio.
- La zona horaria de la aplicación es `America/Lima`.
