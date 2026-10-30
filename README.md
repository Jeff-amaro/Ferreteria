# 1. EJJ - Sistema Web de Gestión de Ventas e Inventario para Ferreterías

## 2. Descripción General

**EJJ** es un sistema web orientado a digitalizar y centralizar la operación comercial y logística de una ferretería. Su propósito es resolver dos problemas críticos del negocio: la lentitud y los errores en la venta en mostrador, y la falta de control confiable sobre las existencias del almacén.

La aplicación integra el catálogo de productos, la atención en punto de venta, la elaboración de cotizaciones, el cobro, la emisión de comprobantes con IGV y el descuento automático de stock. De forma complementaria, incorpora conteos físicos, auditorías por desbalances, reportes de reposición y órdenes de compra, permitiendo que las áreas de ventas y almacén trabajen sobre una misma fuente de información y reduzcan pérdidas, quiebres de stock y diferencias de inventario.

## 3. Stack Tecnológico

| Componente | Tecnología |
| --- | --- |
| Framework | Laravel 13 (PHP 8.3+; entorno de referencia XAMPP con PHP 8.4) |
| Base de datos | MySQL / MariaDB (XAMPP) |
| Frontend | Blade Components, Tailwind CSS 4, Vite |
| Tipografía | Inter |
| Diseño | Basado en prototipo de Figma, paleta principal `#0820d7` |
| Generación de PDF | Laravel DomPDF |
| Autenticación y acceso | Sesiones Laravel, roles y middleware personalizado |
| Pruebas y calidad | PHPUnit y Laravel Pint |

## 4. Estructura del Sistema

### 4.1 Mantenimientos CRUD (perfil Administrador)

| Mantenimiento | Funcionalidad principal |
| --- | --- |
| Productos | Registro, edición, eliminación lógica, restauración, control de precios, costo, stock y stock mínimo |
| Categorías | Organización del catálogo y clasificación de productos |
| Clientes | Registro de datos personales, documento, contacto, dirección y tipo de cliente (minorista/mayorista) |
| Proveedores | Registro de razón social, RUC, contacto y datos de abastecimiento |

Adicionalmente, el administrador puede gestionar usuarios, roles y perfiles de acceso.

### 4.2 Perfiles de Acceso

| Perfil | Alcance |
| --- | --- |
| Administrador | Acceso total: CRUDs, ventas, inventario, compras, auditorías, mayoristas, campañas y alertas |
| Vendedor | Catálogo, punto de venta, cotizaciones, órdenes, pagos, comprobantes, mayoristas y campañas |
| Almacenero | Órdenes de venta, inventario físico, auditorías, reposición, compras y alertas |
| Cliente | Registro, inicio de sesión, consulta del catálogo público y seguimiento de su cuenta |

## 5. Procesos Clave de Negocio

### 5.1 Proceso principal: cotización, cobro y venta

1. El vendedor registra la venta desde el punto de venta y genera una **cotización (`COT`)**.
2. El sistema calcula subtotal, descuentos por volumen y descuento adicional, envío e **IGV del 18%**.
3. La cotización puede emitirse en PDF, aceptarse, rechazarse o convertirse en una **orden de venta (`OV`)**.
4. Al convertir la cotización, el sistema **descuenta automáticamente el stock** de los productos.
5. El cobro se registra en efectivo o medio electrónico y se emite el comprobante correspondiente:
   - **Boleta** para DNI, serie `B001`.
   - **Factura** para RUC, serie `F001`.
6. La orden avanza por los estados `pendiente`, `en_preparacion`, `despachado` y `entregado`. Si se anula antes del despacho, el stock se restaura.

Fórmula aplicada:

```text
total = subtotal - descuentos + envío + IGV (18%)
```

### 5.2 Proceso secundario: control de almacén y abastecimiento

1. El almacenero registra un **conteo físico** de los productos.
2. El sistema compara el conteo con el stock del sistema y detecta **desbalances**.
3. Cada diferencia genera una **auditoría** y una **alerta** para revisión.
4. Al resolver la auditoría, el stock se ajusta al conteo físico aprobado.
5. El sistema genera la **lista de reposición** de productos por debajo del stock mínimo.
6. Las **órdenes de compra** permiten registrar pedidos a proveedores y, al recibirlos, actualizan stock y costo.
7. El panel de **alertas** concentra avisos de stock bajo, desbalances y compras pendientes.

## 6. Cobertura de Casos de Uso

Matriz resumen de los 22 casos de uso del sistema:

| CUS | Caso de uso | Actor principal | Resultado esperado |
| --- | --- | --- | --- |
| CUS-01 | Registro de cliente | Cliente | Cuenta creada con rol Cliente |
| CUS-02 | Inicio y cierre de sesión | Todos | Acceso autenticado según rol |
| CUS-03 | Gestión de productos | Administrador | Producto creado, editado, eliminado o restaurado |
| CUS-04 | Gestión de categorías | Administrador | Categoría registrada o actualizada |
| CUS-05 | Gestión de clientes | Administrador | Cliente registrado o actualizado |
| CUS-06 | Gestión de proveedores | Administrador | Proveedor registrado o actualizado |
| CUS-07 | Consulta de catálogo público | Cliente / Visitante | Productos visibles con precio y disponibilidad |
| CUS-08 | Búsqueda y filtrado de productos | Cliente / Visitante | Resultados filtrados por texto, categoría y stock |
| CUS-09 | Registro de venta en punto de venta | Vendedor | Carrito procesado y cotización generada |
| CUS-10 | Cálculo de cotización con IGV | Vendedor | Totales, descuentos e IGV calculados |
| CUS-11 | Emisión de cotización en PDF | Vendedor | Documento `COT` descargable |
| CUS-12 | Conversión de cotización a orden | Vendedor | Orden `OV` creada y stock descontado |
| CUS-13 | Registro de pago y comprobante | Vendedor | Pago registrado y boleta/factura emitida |
| CUS-14 | Actualización de estado de orden | Vendedor / Almacenero | Orden actualizada con historial |
| CUS-15 | Confirmación de entrega | Vendedor / Almacenero | Orden marcada como entregada |
| CUS-16 | Evaluación de descuento mayorista | Administrador | Solicitud aprobada o rechazada |
| CUS-17 | Envío de campaña o catálogo digital | Administrador / Vendedor | Correo enviado a clientes registrados |
| CUS-18 | Conteo físico de inventario | Almacenero | Conteo registrado con diferencias detectadas |
| CUS-19 | Resolución de auditoría por desbalance | Administrador / Almacenero | Auditoría resuelta y stock ajustado |
| CUS-20 | Consulta de lista de reposición | Almacenero | Productos bajo stock mínimo identificados |
| CUS-21 | Orden de compra y recepción | Almacenero | Compra registrada y stock actualizado |
| CUS-22 | Consulta de alertas y panel de control | Administrador / Almacenero | Indicadores y alertas del sistema visibles |

## 7. Guía de Instalación y Configuración

### 7.1 Requisitos previos

- PHP 8.3 o superior con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` y `zip`.
- Composer.
- Node.js 20+ y npm.
- MySQL 8 o MariaDB 10.4+ (XAMPP).
- Git.

### 7.2 Clonar el repositorio

```bash
git clone https://github.com/Jeff-amaro/Ferreteria.git
cd Ferreteria
```

### 7.3 Instalar dependencias

```bash
composer install
npm install
npm run build
```

### 7.4 Configurar el entorno

Crear el archivo `.env` a partir de `.env.example`:

```bash
copy .env.example .env
```

Generar la clave de aplicación:

```bash
php artisan key:generate
```

Crear la base de datos:

```sql
CREATE DATABASE ferreteria_ejj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configurar la conexión en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ferreteria_ejj
DB_USERNAME=root
DB_PASSWORD=
```

### 7.5 Ejecutar migraciones y datos de demostración

```bash
php artisan migrate --seed
```

Para reiniciar la base de datos desde cero:

```bash
php artisan migrate:fresh --seed
```

Crear el enlace de almacenamiento público:

```bash
php artisan storage:link
```

### 7.6 Iniciar la aplicación

```bash
php artisan serve
```

Abrir `http://127.0.0.1:8000` en el navegador.

## 8. Credenciales de Prueba (Entorno Local)

Las siguientes cuentas son exclusivamente para el entorno local de demostración:

| Perfil | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@ferreteria.com` | `password` |
| Vendedor | `vendedor@ferreteria.com` | `password` |
| Almacenero | `almacen@ferreteria.com` | `password` |

> Estas credenciales deben reemplazarse antes de desplegar el sistema en un entorno productivo.
