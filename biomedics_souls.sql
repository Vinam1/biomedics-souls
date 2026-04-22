-- ============================================================
-- biomedics_souls.sql  — Archivo principal
-- Motor : MariaDB 10.4+  /  MySQL 8+
-- Última revisión: 2026-04-19
--
-- CONTIENE
--   1. Tablas
--   2. Datos iniciales
--   3. Triggers seguros (reseñas + auditoría de estados)
--   4. Stored Procedure de reseñas (usado por los triggers)
--
-- NO CONTIENE (ver biomedics_souls_triggers_opcionales.sql)
--   - trg_carritos_un_activo          → bloqueaba Cart::syncToDatabase()
--   - trg_carritos_items_snapshot_bi  → bloqueaba inserts en carritos_items
--   - trg_carritos_items_snapshot_bu  → bloqueaba updates en carritos_items
--   - trg_pedidos_total_insert        → bloqueaba inserts desde PHP por
--   - trg_pedidos_total_update          precisión de punto flotante
--   - sp_convertir_carrito_en_pedido  → reemplazado por Pedido::createDirect()
--   - sp_generar_numero_pedido        → reemplazado por lógica PHP en Pedido.php
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;
/*!40101 SET NAMES utf8mb4 */;

-- ------------------------------------------------------------
-- Base de datos
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `biomedics_souls`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `biomedics_souls`;

-- ============================================================
-- 1. TABLAS BASE
-- ============================================================

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`            int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`        varchar(100)     NOT NULL,
  `apellidos`     varchar(100)     NOT NULL,
  `email`         varchar(150)     NOT NULL,
  `role`          enum('cliente','admin','superadmin') NOT NULL DEFAULT 'cliente',
  `telefono`      varchar(10)      DEFAULT NULL,
  `password_hash` varchar(255)     NOT NULL,
  `created_at`    timestamp        NOT NULL DEFAULT current_timestamp(),
  `updated_at`    timestamp        NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`    timestamp        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_email` (`email`),
  CONSTRAINT `chk_usuarios_telefono`
    CHECK (`telefono` IS NULL OR `telefono` REGEXP '^[0-9]{10}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `categorias` (
  `id`         int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`     varchar(100) NOT NULL,
  `slug`       varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categorias_nombre` (`nombre`),
  UNIQUE KEY `uq_categorias_slug`   (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `formas_presentacion` (
  `id`         int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`     varchar(50)  NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_formas_presentacion_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Etiquetas visuales para productos (badges en tarjetas)
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `etiquetas` (
  `id`         int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`     varchar(80) NOT NULL,
  `slug`       varchar(80) NOT NULL,
  `color`      varchar(7)  NOT NULL DEFAULT '#3B82F6',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_etiquetas_nombre` (`nombre`),
  UNIQUE KEY `uq_etiquetas_slug`   (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. CATÁLOGO DE PRODUCTOS
-- ============================================================

CREATE TABLE IF NOT EXISTS `productos` (
  `id`                    int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoria_id`          int(10) UNSIGNED NOT NULL,
  `forma_id`              int(10) UNSIGNED NOT NULL,
  `nombre`                varchar(150) NOT NULL,
  `slug`                  varchar(150) NOT NULL,
  `sku`                   varchar(50)  NOT NULL,
  `precio`                decimal(10,2) NOT NULL,
  `precio_descuento`      decimal(10,2) DEFAULT NULL,
  `descripcion_corta`     varchar(300) DEFAULT NULL,
  `descripcion_larga`     mediumtext   DEFAULT NULL,
  `modo_empleo`           text         DEFAULT NULL,
  `usos`                  text         DEFAULT NULL,
  `beneficios`            text         DEFAULT NULL,
  `contenido_neto`        varchar(50)  DEFAULT NULL,
  `cantidad_envase`       varchar(50)  DEFAULT NULL,
  `destacado`             tinyint(1)   NOT NULL DEFAULT 0,
  `estatus`               enum('activo','inactivo','agotado') NOT NULL DEFAULT 'activo',
  -- Columnas desnormalizadas, actualizadas automáticamente por triggers de reseñas
  `calificacion_promedio` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_resenas`         int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at`            timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`            timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`            timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_productos_slug` (`slug`),
  UNIQUE KEY `uq_productos_sku`  (`sku`),
  KEY `idx_productos_forma`             (`forma_id`),
  KEY `idx_productos_categoria_estatus` (`categoria_id`,`estatus`,`deleted_at`),
  KEY `idx_productos_estatus_deleted`   (`estatus`,`deleted_at`),
  KEY `idx_productos_destacado`         (`destacado`,`estatus`),
  CONSTRAINT `fk_productos_categoria`
    FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_productos_forma`
    FOREIGN KEY (`forma_id`) REFERENCES `formas_presentacion` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_productos_precio`
    CHECK (`precio` >= 0),
  CONSTRAINT `chk_productos_precio_descuento`
    CHECK (`precio_descuento` IS NULL OR (`precio_descuento` >= 0 AND `precio_descuento` <= `precio`)),
  CONSTRAINT `chk_productos_calificacion`
    CHECK (`calificacion_promedio` BETWEEN 0 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `productos_imagenes` (
  `id`           int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id`  int(10) UNSIGNED NOT NULL,
  `url_imagen`   varchar(255) NOT NULL,
  `alt_text`     varchar(255) DEFAULT NULL,
  `es_principal` tinyint(1)   NOT NULL DEFAULT 0,
  `orden`        int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at`   timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`   timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`   timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_productos_imagenes_orden`         (`producto_id`,`orden`),
  KEY           `idx_productos_imagenes_principal` (`producto_id`,`es_principal`),
  CONSTRAINT `fk_productos_imagenes_producto`
    FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Relación muchos a muchos: productos ↔ etiquetas
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `productos_etiquetas` (
  `producto_id` int(10) UNSIGNED NOT NULL,
  `etiqueta_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`producto_id`,`etiqueta_id`),
  KEY `idx_productos_etiquetas_etiqueta` (`etiqueta_id`),
  CONSTRAINT `fk_productos_etiquetas_producto`
    FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_productos_etiquetas_etiqueta`
    FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. CARRITOS
-- ============================================================
-- Nota: las tablas de carrito se conservan para trazabilidad.
-- La lógica de conversión carrito→pedido ya NO usa triggers;
-- se maneja en PHP mediante Pedido::createDirect().
-- ============================================================

CREATE TABLE IF NOT EXISTS `carritos` (
  `id`         int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(100)    DEFAULT NULL,
  `estado`     enum('activo','convertido','abandonado') NOT NULL DEFAULT 'activo',
  `version`    int(10) UNSIGNED NOT NULL DEFAULT 1,
  `expires_at` datetime        DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_at`  timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_carritos_cliente_estado`  (`cliente_id`,`estado`),
  KEY `idx_carritos_session_id`      (`session_id`),
  KEY `idx_carritos_session_cliente` (`session_id`,`cliente_id`),
  CONSTRAINT `fk_carritos_cliente`
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `carritos_items` (
  `id`                       int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrito_id`               int(10) UNSIGNED NOT NULL,
  `producto_id`              int(10) UNSIGNED NOT NULL,
  `cantidad`                 int(10) UNSIGNED NOT NULL,
  `precio_unitario_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00,
  `version`                  int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at`               timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`               timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_carritos_items_producto`  (`carrito_id`,`producto_id`),
  KEY        `idx_carritos_items_producto` (`producto_id`),
  CONSTRAINT `fk_carritos_items_carrito`
    FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_carritos_items_producto`
    FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. CLIENTES — DIRECCIONES Y MÉTODOS DE PAGO
-- ============================================================

CREATE TABLE IF NOT EXISTS `direcciones` (
  `id`              int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`      int(10) UNSIGNED NOT NULL,
  `calle`           varchar(150) NOT NULL,
  `numero_exterior` varchar(20)  NOT NULL,
  `numero_interior` varchar(20)  DEFAULT NULL,
  `colonia`         varchar(100) NOT NULL,
  `ciudad`          varchar(100) NOT NULL,
  `estado`          varchar(100) NOT NULL,
  `pais`            varchar(100) NOT NULL DEFAULT 'Mexico',
  `codigo_postal`   varchar(5)   NOT NULL,
  `referencias`     varchar(255) DEFAULT NULL,
  `es_principal`    tinyint(1)   NOT NULL DEFAULT 0,
  `created_at`      timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`      timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`      timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_direcciones_cliente`           (`cliente_id`),
  KEY `idx_direcciones_cliente_principal` (`cliente_id`,`es_principal`),
  CONSTRAINT `fk_direcciones_cliente`
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `metodos_pago` (
  `id`                int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`        int(10) UNSIGNED NOT NULL,
  `tipo`              enum('tarjeta','mercado_pago','spei','oxxo','transferencia','otro') NOT NULL,
  `brand`             varchar(50)  DEFAULT NULL,
  `ultimo_cuatro`     char(4)      DEFAULT NULL,
  `tipo_tarjeta`      enum('credito','debito') DEFAULT NULL,
  `token`             varchar(255) DEFAULT NULL,
  `payment_method_id` varchar(100) DEFAULT NULL,
  `customer_id`       varchar(100) DEFAULT NULL,
  `nickname`          varchar(100) DEFAULT NULL,
  `es_predeterminado` tinyint(1)   NOT NULL DEFAULT 0,
  `activo`            tinyint(1)   NOT NULL DEFAULT 1,
  `created_at`        timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`        timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`        timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_metodos_pago_cliente`         (`cliente_id`),
  KEY `idx_metodos_pago_cliente_activo`  (`cliente_id`,`activo`),
  KEY `idx_metodos_pago_cliente_default` (`cliente_id`,`es_predeterminado`),
  KEY `idx_metodos_pago_customer`        (`customer_id`),
  KEY `idx_metodos_pago_payment_method`  (`payment_method_id`),
  CONSTRAINT `fk_metodos_pago_cliente`
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PEDIDOS
-- ============================================================

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id`                        int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_pedido`             varchar(30)   NOT NULL,
  `cliente_id`                int(10) UNSIGNED NOT NULL,
  `direccion_envio_id`        int(10) UNSIGNED DEFAULT NULL,
  `metodo_pago_id`            int(10) UNSIGNED DEFAULT NULL,
  `estado_pedido`             enum('pendiente','pagado','en_preparacion','enviado','entregado','cancelado')
                              NOT NULL DEFAULT 'pendiente',
  `subtotal`                  decimal(10,2) NOT NULL,
  `costo_envio`               decimal(10,2) NOT NULL DEFAULT 0.00,
  `total`                     decimal(10,2) NOT NULL,
  `moneda`                    char(3)       NOT NULL DEFAULT 'MXN',
  `mp_preference_id`          varchar(100)  DEFAULT NULL,
  `mp_payment_id`             varchar(100)  DEFAULT NULL,
  `mp_status`                 varchar(50)   DEFAULT NULL,
  `mp_status_detail`          varchar(100)  DEFAULT NULL,
  `pagado_at`                 timestamp NULL DEFAULT NULL,
  `cancelado_at`              timestamp NULL DEFAULT NULL,
  -- Snapshot de la dirección al momento de confirmar el pedido
  `direccion_nombre_completo` varchar(200)  DEFAULT NULL,
  `direccion_telefono`        varchar(20)   DEFAULT NULL,
  `direccion_calle`           varchar(150)  DEFAULT NULL,
  `direccion_numero_exterior` varchar(20)   DEFAULT NULL,
  `direccion_numero_interior` varchar(20)   DEFAULT NULL,
  `direccion_colonia`         varchar(100)  DEFAULT NULL,
  `direccion_ciudad`          varchar(100)  DEFAULT NULL,
  `direccion_estado`          varchar(100)  DEFAULT NULL,
  `direccion_pais`            varchar(100)  DEFAULT NULL,
  `direccion_codigo_postal`   varchar(10)   DEFAULT NULL,
  `direccion_referencias`     varchar(255)  DEFAULT NULL,
  `created_at`                timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`                timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`                timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pedidos_numero_pedido`  (`numero_pedido`),
  KEY `idx_pedidos_cliente`              (`cliente_id`),
  KEY `idx_pedidos_direccion`            (`direccion_envio_id`),
  KEY `idx_pedidos_metodo_pago`          (`metodo_pago_id`),
  KEY `idx_pedidos_estado_created_at`    (`estado_pedido`,`created_at`),
  KEY `idx_pedidos_cliente_estado`       (`cliente_id`,`estado_pedido`),
  KEY `idx_pedidos_mp_payment`           (`mp_payment_id`),
  CONSTRAINT `fk_pedidos_cliente`
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_direccion`
    FOREIGN KEY (`direccion_envio_id`) REFERENCES `direcciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_metodo_pago`
    FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `pedidos_detalle` (
  `id`               int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`        int(10) UNSIGNED NOT NULL,
  `producto_id`      int(10) UNSIGNED NOT NULL,
  `producto_nombre`  varchar(150) NOT NULL,
  `producto_sku`     varchar(50)  NOT NULL,
  `cantidad`         int(10) UNSIGNED NOT NULL,
  `precio_original`  decimal(10,2) NOT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL,
  `precio_unitario`  decimal(10,2) NOT NULL,
  `subtotal`         decimal(10,2) NOT NULL,
  `created_at`       timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`       timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pedidos_detalle_producto`  (`pedido_id`,`producto_id`),
  KEY        `idx_pedidos_detalle_producto` (`producto_id`),
  CONSTRAINT `fk_pedidos_detalle_pedido`
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_detalle_producto`
    FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Registro de transacciones procesadas por la pasarela simulada
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `pagos_transacciones` (
  `id`               int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`        int(10) UNSIGNED NOT NULL,
  `cliente_id`       int(10) UNSIGNED NOT NULL,
  `metodo_pago_id`   int(10) UNSIGNED DEFAULT NULL,
  `gateway`          varchar(50)  NOT NULL,
  `referencia`       varchar(100) NOT NULL,
  `estado`           varchar(50)  NOT NULL,
  `detalle`          varchar(255) DEFAULT NULL,
  `monto`            decimal(10,2) NOT NULL,
  `moneda`           char(3)      NOT NULL DEFAULT 'MXN',
  `payload_respuesta` longtext    DEFAULT NULL,
  `created_at`       timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`       timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pagos_transacciones_referencia` (`referencia`),
  KEY `idx_pagos_transacciones_pedido` (`pedido_id`),
  KEY `idx_pagos_transacciones_cliente` (`cliente_id`),
  KEY `idx_pagos_transacciones_metodo` (`metodo_pago_id`),
  CONSTRAINT `fk_pagos_transacciones_pedido`
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pagos_transacciones_cliente`
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pagos_transacciones_metodo`
    FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Historial de cambios de estado de pedidos
-- Alimentado automáticamente por el trigger trg_pedidos_auditoria
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `pedidos_historial_estados` (
  `id`                      int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id`               int(10) UNSIGNED NOT NULL,
  `estado_anterior`         enum('pendiente','pagado','en_preparacion','enviado','entregado','cancelado') DEFAULT NULL,
  `estado_nuevo`            enum('pendiente','pagado','en_preparacion','enviado','entregado','cancelado') NOT NULL,
  `fecha_cambio`            timestamp NOT NULL DEFAULT current_timestamp(),
  `cambiado_por_usuario_id` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_historial_pedido_fecha` (`pedido_id`,`fecha_cambio`),
  KEY `idx_pedidos_historial_usuario`      (`cambiado_por_usuario_id`),
  CONSTRAINT `fk_pedidos_historial_pedido`
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_historial_usuario`
    FOREIGN KEY (`cambiado_por_usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla de secuencia para numeración de pedidos
-- PHP la usa con INSERT ... ON DUPLICATE KEY UPDATE
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `pedidos_secuencia` (
  `fecha`     date         NOT NULL,
  `secuencia` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. RESEÑAS
-- ============================================================

CREATE TABLE IF NOT EXISTS `resenas` (
  `id`           int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`   int(10) UNSIGNED NOT NULL,
  `producto_id`  int(10) UNSIGNED NOT NULL,
  `calificacion` tinyint(3) UNSIGNED NOT NULL,
  `titulo`       varchar(150) DEFAULT NULL,
  `comentario`   text         DEFAULT NULL,
  `estatus`      enum('publicada','eliminada') NOT NULL DEFAULT 'publicada',
  `created_at`   timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`   timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at`   timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_resenas_cliente_producto`     (`cliente_id`,`producto_id`),
  KEY           `idx_resenas_producto_estatus` (`producto_id`,`estatus`),
  KEY           `idx_resenas_cliente`          (`cliente_id`),
  CONSTRAINT `fk_resenas_cliente`
    FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_resenas_producto`
    FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_resenas_calificacion`
    CHECK (`calificacion` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 7. DATOS INICIALES
-- ============================================================

INSERT IGNORE INTO `categorias` (`id`, `nombre`, `slug`) VALUES
(2, 'Natural', 'natural');

INSERT IGNORE INTO `formas_presentacion` (`id`, `nombre`) VALUES
(1, 'Serum'),
(2, 'Capsulas'),
(3, 'Polvo'),
(4, 'Crema');

INSERT IGNORE INTO `etiquetas` (`id`, `nombre`, `slug`, `color`) VALUES
(1, 'Antioxidante', 'antioxidante', '#3b82f6');

-- Contraseña de jhony: jhony123  |  contraseña de roberto: roberto123
INSERT IGNORE INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `role`, `password_hash`) VALUES
(1, 'jhony',   'joestar', 'jhony@gmail.com',   'admin',   '$2y$10$tkBEDTBtvGxwqST/0xfEpeH8lRqxTe/cvFtgx0v8kCrwl5SQQdM1y'),
(2, 'roberto', 'gomez',   'roberto@gmail.com', 'cliente', '$2y$10$bafgz5oeXDfegBoaHwr3reIoHgSLTCcsZd2Fpr7vnAxHDZPaKtgS.');

INSERT IGNORE INTO `productos`
  (`id`, `categoria_id`, `forma_id`, `nombre`, `slug`, `sku`,
   `precio`, `descripcion_corta`, `descripcion_larga`,
   `modo_empleo`, `usos`, `beneficios`, `contenido_neto`, `destacado`, `estatus`)
VALUES
  (1, 2, 2, 'Vinagre de Manzana', 'vinagre', 'VIN-001',
   100.00, 'Vinagre de manzana premium', 'Es vinagre de manzana orgánico.',
   'Tomar una cucharada disuelta en agua.', 'Para cabello y digestión.', 'Muy bueno para la salud.',
   '100 mg', 0, 'activo');

INSERT IGNORE INTO `productos_imagenes` (`id`, `producto_id`, `url_imagen`, `es_principal`, `orden`) VALUES
(1, 1, '1_1775724836_logo69d769242b09a5.26461709.jpeg', 1, 0);

-- ============================================================
-- 8. STORED PROCEDURES
-- ============================================================

DROP PROCEDURE IF EXISTS `sp_recalcular_resenas_producto`;
DROP PROCEDURE IF EXISTS `sp_set_metodo_pago_predeterminado`;

DELIMITER $$

-- ------------------------------------------------------------
-- sp_recalcular_resenas_producto
-- Recalcula calificacion_promedio y total_resenas en productos.
-- Lo llaman los triggers de reseñas automáticamente.
-- ------------------------------------------------------------
CREATE PROCEDURE `sp_recalcular_resenas_producto`(IN `p_producto_id` INT UNSIGNED)
BEGIN
  UPDATE `productos`
  SET
    `total_resenas` = (
      SELECT COUNT(*)
      FROM `resenas`
      WHERE `producto_id` = p_producto_id
        AND `estatus`     = 'publicada'
        AND `deleted_at`  IS NULL
    ),
    `calificacion_promedio` = (
      SELECT COALESCE(AVG(`calificacion`), 0)
      FROM `resenas`
      WHERE `producto_id` = p_producto_id
        AND `estatus`     = 'publicada'
        AND `deleted_at`  IS NULL
    )
  WHERE `id` = p_producto_id;
END$$

-- ------------------------------------------------------------
-- sp_set_metodo_pago_predeterminado
-- Cambia el método de pago predeterminado de un cliente.
-- Se puede usar desde PHP cuando el usuario seleccione uno.
-- ------------------------------------------------------------
CREATE PROCEDURE `sp_set_metodo_pago_predeterminado`(
  IN `p_cliente_id`     INT UNSIGNED,
  IN `p_metodo_pago_id` INT UNSIGNED
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

  UPDATE `metodos_pago`
    SET `es_predeterminado` = 0
  WHERE `cliente_id` = p_cliente_id;

  UPDATE `metodos_pago`
    SET `es_predeterminado` = 1
  WHERE `id`         = p_metodo_pago_id
    AND `cliente_id` = p_cliente_id
    AND `activo`     = 1
    AND `deleted_at` IS NULL;

  IF ROW_COUNT() = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'El metodo de pago no pertenece al cliente o no esta disponible';
  END IF;

  COMMIT;
END$$

DELIMITER ;

-- ============================================================
-- 9. TRIGGERS
-- ============================================================

-- ------------------------------------------------------------
-- trg_pedidos_auditoria
-- Registra cada cambio de estado de un pedido en el historial.
-- PHP debe hacer: SET @current_user_id = <id> antes del UPDATE.
-- ------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_pedidos_auditoria`;

DELIMITER $$
CREATE TRIGGER `trg_pedidos_auditoria`
AFTER UPDATE ON `pedidos`
FOR EACH ROW
BEGIN
  IF OLD.estado_pedido <> NEW.estado_pedido THEN
    INSERT INTO `pedidos_historial_estados`
      (`pedido_id`, `estado_anterior`, `estado_nuevo`, `cambiado_por_usuario_id`)
    VALUES
      (NEW.id, OLD.estado_pedido, NEW.estado_pedido, @current_user_id);
  END IF;
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- Triggers de reseñas
-- Mantienen actualizados calificacion_promedio y total_resenas
-- en la tabla productos cada vez que se inserta/modifica/borra
-- una reseña.
-- ------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_resenas_after_insert`;
DROP TRIGGER IF EXISTS `trg_resenas_after_update`;
DROP TRIGGER IF EXISTS `trg_resenas_after_delete`;
DROP TRIGGER IF EXISTS `trg_resenas_verificar_compra`;

DELIMITER $$

CREATE TRIGGER `trg_resenas_after_insert`
AFTER INSERT ON `resenas`
FOR EACH ROW
BEGIN
  CALL `sp_recalcular_resenas_producto`(NEW.producto_id);
END$$

CREATE TRIGGER `trg_resenas_after_update`
AFTER UPDATE ON `resenas`
FOR EACH ROW
BEGIN
  IF OLD.producto_id <> NEW.producto_id THEN
    CALL `sp_recalcular_resenas_producto`(OLD.producto_id);
  END IF;
  CALL `sp_recalcular_resenas_producto`(NEW.producto_id);
END$$

CREATE TRIGGER `trg_resenas_after_delete`
AFTER DELETE ON `resenas`
FOR EACH ROW
BEGIN
  CALL `sp_recalcular_resenas_producto`(OLD.producto_id);
END$$

-- ------------------------------------------------------------
-- trg_resenas_verificar_compra
-- Impide que un cliente reseñe un producto que no haya recibido.
-- NOTA: comenta o elimina este trigger solo si necesitas hacer
--       pruebas sin haber completado pedidos.
-- ------------------------------------------------------------
CREATE TRIGGER `trg_resenas_verificar_compra`
BEFORE INSERT ON `resenas`
FOR EACH ROW
BEGIN
  DECLARE v_comprado INT DEFAULT 0;

  SELECT COUNT(*) INTO v_comprado
  FROM `pedidos_detalle` pd
  INNER JOIN `pedidos` p ON p.id = pd.pedido_id
  WHERE pd.producto_id  = NEW.producto_id
    AND p.cliente_id    = NEW.cliente_id
    AND p.estado_pedido = 'entregado'
    AND p.deleted_at    IS NULL;

  IF v_comprado = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Debes haber recibido el producto para poder resenarlo';
  END IF;
END$$

DELIMITER ;

-- ============================================================
-- FIN  biomedics_souls.sql
-- ============================================================
