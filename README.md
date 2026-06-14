# Biomedics Souls - Aplicación MVC en PHP

Proyecto de tienda en línea construido con PHP, MySQL, Bootstrap y JavaScript usando una arquitectura MVC simple.

## Características principales

- Carrito de compras con resumen de pedido
- Checkout con pago por OpenPay para tarjetas Visa/Mastercard
- Tokenización de tarjeta en el cliente usando el SDK de OpenPay
- Almacenamiento de resultados de pago y transacciones en la base de datos
- Reseñas y valoraciones de productos en la página de detalle
- Panel de administración para productos, pedidos y clientes
- Gestión de direcciones y métodos de pago por usuario

## Configuración

1. Copia la carpeta `biomedics-souls` a `C:\xampp\htdocs`.
2. Importa `biomedics_souls.sql` en `phpMyAdmin` o desde la línea de comandos para crear la base de datos.
3. Asegúrate de que la carpeta `public/` esté disponible en tu servidor local.
4. Coloca las credenciales y claves en un archivo `.env` en la raíz del proyecto o ajusta `public/app/config/config.php`.

Variables clave compatibles:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `OPENPAY_API_KEY`
- `OPENPAY_MERCHANT_ID`
- `OPENPAY_BASE_URL` (opcional, por defecto `https://api.openpay.mx/v1`)

## Dependencias

- PHP 8+ con `curl` habilitado
- MySQL
- XAMPP o servidor local equivalente

## Uso en XAMPP

1. Abre `http://localhost/phpmyadmin`.
2. Importa `biomedics_souls.sql`.
3. Navega a `http://localhost/biomedics-souls/public/`.

## Estructura del proyecto

- `public/` - Punto de entrada, activos públicos y archivos públicos.
- `public/app/config/` - Configuración general y seguridad.
- `public/app/core/` - Núcleo MVC: `App`, `Controller`, `Database`.
- `public/app/controllers/` - Controladores del sitio.
- `public/app/models/` - Modelos para la base de datos.
- `public/app/services/` - Servicios reutilizables, incluida la integración con OpenPay.
- `public/app/views/` - Plantillas de presentación.

## Rutas principales

- `/` o `/home` - Página principal.
- `/catalogo` - Catálogo de productos.
- `/producto/{slug}` - Detalle de producto y reseñas.
- `/carrito` - Vista del carrito.
- `/checkout` - Checkout y pago con OpenPay.
- `/auth/login` - Iniciar sesión.
- `/auth/register` - Registro de usuario.
- `/pedido/exito` - Confirmación de pedido.
- `/pedido/fallo` - Pago fallido.
- `/cuenta` - Panel de usuario.
- `/admin/dashboard` - Panel de administración.

## Notas de implementación

- El checkout carga el SDK de OpenPay desde `https://openpay.s3.amazonaws.com/openpay.v1.0.min.js`.
- El backend procesa pagos con `public/app/services/OpenPayService.php`.
- Los métodos de pago guardados pueden reutilizarse, y el flujo de tarjeta nueva usa tokenización segura.
- Las reseñas de producto se muestran en cada página de producto y se almacenan con validación de texto.

## Consideraciones

- Si usas un host virtual, ajusta la ruta base en `public/.htaccess`.
- Revisa que la clave API de OpenPay (`OPENPAY_API_KEY`) esté disponible para tokenizar la tarjeta en cliente.
- Verifica que el ID de comerciante (`OPENPAY_MERCHANT_ID`) sea válido para el ambiente elegido.

## Próximos pasos

- Extender administración de pagos y tarjetas guardadas.
- Agregar soporte de cupones y descuentos.
- Mejorar validación y mensajes de error en checkout.
- Añadir pruebas unitarias y de integración.
