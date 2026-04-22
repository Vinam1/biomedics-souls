# Biomedics Souls - Aplicación MVC en PHP

Este proyecto es un sitio web básico construido con PHP, Bootstrap, JavaScript y una base de datos MySQL.
La arquitectura está organizada en MVC (Modelos, Vistas y Controladores).

## Cómo usarlo en XAMPP

1. Copia la carpeta `biomedics-souls` dentro de `C:\xampp\htdocs`.
2. Importa el archivo `biomedics_souls.sql` en `phpMyAdmin` o desde la línea de comandos:
   - `http://localhost/phpmyadmin`
   - Importa el archivo y crea la base de datos `biomedics_souls`
3. Abre el proyecto en el navegador:
   - `http://localhost/biomedics-souls/public/`

## Estructura de carpetas

- `public/` - Front controller, activos públicos y ruta base.
- `app/core/` - Clases del núcleo: `App`, `Controller`, `Database`.
- `app/controllers/` - Controladores que reciben las rutas.
- `app/models/` - Modelos de datos para la base de datos.
- `app/views/` - Vistas para mostrar la información.
- `app/config/` - Configuración de base de datos y rutas.

## Rutas disponibles

- `/` o `/home` - Página de inicio con hero y productos destacados.
- `/catalogo` - Listado de productos en tarjetas.
- `/producto/{slug}` o `/product/{slug}` - Página de detalle del producto.
- `/carrito` - Tabla del carrito.
- `/checkout` - Resumen de pedido con botón de Mercado Pago.
- `/cuenta` - Dashboard de usuario con perfil y pedidos recientes.
- `/ciencia` - Página de ciencia e investigación.
- `/faq` - Preguntas frecuentes con acordeones.
- `/contacto` - Formulario de contacto y tarjetas de soporte.
- `/quiz` - Quiz interactivo con una pregunta por pantalla.
- `/auth/login` - Inicio de sesión.
- `/auth/register` - Registro de usuarios.
- `/pedido/exito` - Página de pedido exitoso.
- `/pedido/fallo` - Página de pago fallido.
- `/admin/dashboard` - Panel de administración (acceso solo para admin).
- `/admin/productos` - Gestión de productos.
- `/admin/producto-form` - Crear nuevo producto.
- `/admin/producto-form/{id}` - Editar producto existente.
- `/admin/pedidos` - Ver pedidos.
- `/admin/pedido-detalle/{id}` - Detalle de pedido.
- Cualquier ruta no válida mostrará el error 404.

## Ajustes importantes

- Si tu aplicación está en otra carpeta o usas un host virtual, actualiza `RewriteBase` en `public/.htaccess`.
- Verifica que los datos existan en la base de datos. Si no hay productos o categorías, la página mostrará un mensaje informativo.

## Próximos pasos

- Agregar formulario de búsqueda
- Añadir carrito y control de stock por etiquetas
- Implementar administración de productos y etiquetas
- Crear autenticación de usuarios
