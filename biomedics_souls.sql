-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2026 a las 08:26:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biomedics_souls`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_recalcular_resenas_producto` (IN `p_producto_id` INT UNSIGNED)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_metodo_pago_predeterminado` (IN `p_cliente_id` INT UNSIGNED, IN `p_metodo_pago_id` INT UNSIGNED)   BEGIN
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

CREATE TABLE `carritos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `estado` enum('activo','convertido','abandonado') NOT NULL DEFAULT 'activo',
  `version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos_items`
--

CREATE TABLE `carritos_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `carrito_id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `precio_unitario_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00,
  `version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Rendimiento y Energía', 'rendimiento-y-energia', '2026-04-23 05:15:03', '2026-04-23 05:15:03', NULL),
(5, 'Bienestar Mental y Descanso', 'bienestar-mental-y-descanso', '2026-04-23 05:15:15', '2026-04-23 05:15:15', NULL),
(6, 'Salud Digestiva y Detox', 'salud-digestiva-y-detox', '2026-04-23 05:15:28', '2026-04-23 05:15:28', NULL),
(7, 'Inmunidad y Protección', 'inmunidad-y-proteccion', '2026-04-23 05:15:39', '2026-04-23 05:15:39', NULL),
(8, 'Estética y Longevidad', 'estetica-y-longevidad', '2026-04-23 05:15:52', '2026-04-23 05:15:52', NULL),
(9, 'Salud Vascular y Bienestar Celular', 'salud-vascular-y-bienestar-celular', '2026-04-23 06:02:51', '2026-04-23 06:02:51', NULL),
(10, 'Salud Metabolica y Longevidad', 'salud-metabolica-y-longevidad', '2026-04-23 06:11:47', '2026-04-23 06:11:47', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `calle` varchar(150) NOT NULL,
  `numero_exterior` varchar(20) NOT NULL,
  `numero_interior` varchar(20) DEFAULT NULL,
  `colonia` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL DEFAULT 'Mexico',
  `codigo_postal` varchar(5) NOT NULL,
  `referencias` varchar(255) DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id`, `cliente_id`, `calle`, `numero_exterior`, `numero_interior`, `colonia`, `ciudad`, `estado`, `pais`, `codigo_postal`, `referencias`, `es_principal`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'AV. QUINTANA ROO', 'ABEDUL', '355', 'SAN PABLO DE LAS SALINAS', 'LOTE 46', 'MEXICO', 'Mexico', '54938', NULL, 0, '2026-04-20 03:36:34', '2026-04-20 03:36:34', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3B82F6',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`id`, `nombre`, `slug`, `color`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 'PreEntreno', 'preentreno', '#3b82f6', '2026-04-23 05:08:49', '2026-04-23 05:08:49', NULL),
(6, 'EnergíaLimpia', 'energialimpia', '#3b82f6', '2026-04-23 05:09:07', '2026-04-23 05:09:07', NULL),
(7, 'Resistencia', 'resistencia', '#3b82f6', '2026-04-23 05:09:19', '2026-04-23 05:09:19', NULL),
(8, 'FuerzaNatural', 'fuerzanatural', '#3b82f6', '2026-04-23 05:09:30', '2026-04-23 05:09:30', NULL),
(9, 'RecuperaciónMuscular', 'recuperacionmuscular', '#3b82f6', '2026-04-23 05:09:43', '2026-04-23 05:09:43', NULL),
(10, 'AminosNaturales', 'aminosnaturales', '#3b82f6', '2026-04-23 05:09:57', '2026-04-23 05:09:57', NULL),
(11, 'Nootrópicos', 'nootropicos', '#3b82f6', '2026-04-23 05:10:23', '2026-04-23 05:10:23', NULL),
(12, 'RelaxNatural', 'relaxnatural', '#3b82f6', '2026-04-23 05:10:37', '2026-04-23 05:10:37', NULL),
(13, 'SueñoProfundo', 'suenoprofundo', '#3b82f6', '2026-04-23 05:10:49', '2026-04-23 05:10:49', NULL),
(14, 'Enfoque', 'enfoque', '#3b82f6', '2026-04-23 05:11:00', '2026-04-23 05:11:00', NULL),
(15, 'AntiEstrés', 'antiestres', '#3b82f6', '2026-04-23 05:11:11', '2026-04-23 05:11:11', NULL),
(16, 'Adaptógenos', 'adaptogenos', '#3b82f6', '2026-04-23 05:11:19', '2026-04-23 05:11:19', NULL),
(17, 'MenteClara', 'menteclara', '#3b82f6', '2026-04-23 05:11:29', '2026-04-23 05:11:29', NULL),
(18, 'Probióticos', 'probioticos', '#3b82f6', '2026-04-23 05:11:40', '2026-04-23 05:11:40', NULL),
(19, 'DigestiónLigera', 'digestionligera', '#3b82f6', '2026-04-23 05:11:52', '2026-04-23 05:11:52', NULL),
(20, 'Detox', 'detox', '#3b82f6', '2026-04-23 05:12:05', '2026-04-23 05:12:05', NULL),
(21, 'SaludIntestinal', 'saludintestinal', '#3b82f6', '2026-04-23 05:12:15', '2026-04-23 05:12:15', NULL),
(22, 'LimpiaTuCuerpo', 'limpiatucuerpo', '#3b82f6', '2026-04-23 05:12:28', '2026-04-23 05:12:28', NULL),
(23, 'EnzimasNaturales', 'enzimasnaturales', '#3b82f6', '2026-04-23 05:12:40', '2026-04-23 05:12:40', NULL),
(24, 'DefensasFuertes', 'defensasfuertes', '#3b82f6', '2026-04-23 05:12:54', '2026-04-23 05:12:54', NULL),
(25, 'Antioxidantes', 'antioxidantes', '#3b82f6', '2026-04-23 05:13:03', '2026-04-23 05:13:03', NULL),
(26, 'SistemaInmune', 'sistemainmune', '#3b82f6', '2026-04-23 05:13:16', '2026-04-23 05:13:16', NULL),
(27, 'VitaminaNatural', 'vitaminanatural', '#3b82f6', '2026-04-23 05:13:29', '2026-04-23 05:13:29', NULL),
(28, 'ProtecciónDiaria', 'protecciondiaria', '#3b82f6', '2026-04-23 05:13:37', '2026-04-23 05:13:37', NULL),
(29, 'ColágenoNatural', 'colagenonatural', '#3b82f6', '2026-04-23 05:13:49', '2026-04-23 05:13:49', NULL),
(30, 'PielRadiante', 'pielradiante', '#3b82f6', '2026-04-23 05:13:59', '2026-04-23 05:13:59', NULL),
(31, 'CabelloSano', 'cabellosano', '#3b82f6', '2026-04-23 05:14:08', '2026-04-23 05:14:08', NULL),
(32, 'Nutricosmética', 'nutricosmetica', '#3b82f6', '2026-04-23 05:14:16', '2026-04-23 05:14:16', NULL),
(33, 'Antiedad', 'antiedad', '#3b82f6', '2026-04-23 05:14:26', '2026-04-23 05:14:26', NULL),
(34, 'BrilloInterior', 'brillointerior', '#3b82f6', '2026-04-23 05:14:34', '2026-04-23 05:14:34', NULL),
(35, 'MetabolismoActivo', 'metabolismoactivo', '#3b82f6', '2026-04-23 05:24:01', '2026-04-23 05:24:01', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formas_presentacion`
--

CREATE TABLE `formas_presentacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `formas_presentacion`
--

INSERT INTO `formas_presentacion` (`id`, `nombre`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Serum', '2026-04-20 03:33:50', '2026-04-20 03:33:50', NULL),
(2, 'Capsulas', '2026-04-20 03:33:50', '2026-04-20 03:33:50', NULL),
(3, 'Polvo', '2026-04-20 03:33:50', '2026-04-20 03:33:50', NULL),
(4, 'Crema', '2026-04-20 03:33:50', '2026-04-20 03:33:50', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('tarjeta','mercado_pago','spei','oxxo','transferencia','otro') NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `ultimo_cuatro` char(4) DEFAULT NULL,
  `tipo_tarjeta` enum('credito','debito') DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `payment_method_id` varchar(100) DEFAULT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `es_predeterminado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `cliente_id`, `tipo`, `brand`, `ultimo_cuatro`, `tipo_tarjeta`, `token`, `payment_method_id`, `customer_id`, `nickname`, `es_predeterminado`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'mercado_pago', 'visa', NULL, NULL, NULL, 'mercado_pago_bcc3235c', 'cus_3_51b772', 'compras', 0, 1, '2026-04-20 03:36:52', '2026-04-20 03:36:52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_transacciones`
--

CREATE TABLE `pagos_transacciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `metodo_pago_id` int(10) UNSIGNED DEFAULT NULL,
  `gateway` varchar(50) NOT NULL,
  `referencia` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'MXN',
  `payload_respuesta` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero_pedido` varchar(30) NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `direccion_envio_id` int(10) UNSIGNED DEFAULT NULL,
  `metodo_pago_id` int(10) UNSIGNED DEFAULT NULL,
  `estado_pedido` enum('pendiente','pagado','en_preparacion','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'MXN',
  `mp_preference_id` varchar(100) DEFAULT NULL,
  `mp_payment_id` varchar(100) DEFAULT NULL,
  `mp_status` varchar(50) DEFAULT NULL,
  `mp_status_detail` varchar(100) DEFAULT NULL,
  `pagado_at` timestamp NULL DEFAULT NULL,
  `cancelado_at` timestamp NULL DEFAULT NULL,
  `direccion_nombre_completo` varchar(200) DEFAULT NULL,
  `direccion_telefono` varchar(20) DEFAULT NULL,
  `direccion_calle` varchar(150) DEFAULT NULL,
  `direccion_numero_exterior` varchar(20) DEFAULT NULL,
  `direccion_numero_interior` varchar(20) DEFAULT NULL,
  `direccion_colonia` varchar(100) DEFAULT NULL,
  `direccion_ciudad` varchar(100) DEFAULT NULL,
  `direccion_estado` varchar(100) DEFAULT NULL,
  `direccion_pais` varchar(100) DEFAULT NULL,
  `direccion_codigo_postal` varchar(10) DEFAULT NULL,
  `direccion_referencias` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `pedidos`
--
DELIMITER $$
CREATE TRIGGER `trg_pedidos_auditoria` AFTER UPDATE ON `pedidos` FOR EACH ROW BEGIN
  IF OLD.estado_pedido <> NEW.estado_pedido THEN
    INSERT INTO `pedidos_historial_estados`
      (`pedido_id`, `estado_anterior`, `estado_nuevo`, `cambiado_por_usuario_id`)
    VALUES
      (NEW.id, OLD.estado_pedido, NEW.estado_pedido, @current_user_id);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_detalle`
--

CREATE TABLE `pedidos_detalle` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `producto_nombre` varchar(150) NOT NULL,
  `producto_sku` varchar(50) NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `precio_original` decimal(10,2) NOT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_historial_estados`
--

CREATE TABLE `pedidos_historial_estados` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `estado_anterior` enum('pendiente','pagado','en_preparacion','enviado','entregado','cancelado') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','pagado','en_preparacion','enviado','entregado','cancelado') NOT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `cambiado_por_usuario_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_secuencia`
--

CREATE TABLE `pedidos_secuencia` (
  `fecha` date NOT NULL,
  `secuencia` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `forma_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL,
  `descripcion_corta` varchar(300) DEFAULT NULL,
  `descripcion_larga` mediumtext DEFAULT NULL,
  `modo_empleo` text DEFAULT NULL,
  `usos` text DEFAULT NULL,
  `beneficios` text DEFAULT NULL,
  `contenido_neto` varchar(50) DEFAULT NULL,
  `cantidad_envase` varchar(50) DEFAULT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `estatus` varchar(100) DEFAULT NULL,
  `calificacion_promedio` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_resenas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `forma_id`, `nombre`, `slug`, `sku`, `precio`, `precio_descuento`, `descripcion_corta`, `descripcion_larga`, `modo_empleo`, `usos`, `beneficios`, `contenido_neto`, `cantidad_envase`, `destacado`, `estatus`, `calificacion_promedio`, `total_resenas`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 2, 'Vinagre de Manzana', 'vinagre-de-manzana', 'SNS-VIN-MANZ-60C', 310.00, NULL, 'El aliado natural para un metabolismo activo y una digestión ligera. El Vinagre de Manzana SENSËA combina pureza y tradición en una fórmula diseñada para equilibrar tu cuerpo desde el interior, apoyando el control de peso y la desintoxicación diaria de forma consciente.', 'Eleva tu estándar de bienestar con Vinagre de Manzana SENSËA, un suplemento de origen natural formulado para quienes buscan armonía metabólica sin complicaciones. Reconocido ancestralmente por sus propiedades depurativas, este suplemento actúa como un catalizador en tu organismo, optimizando la absorción de nutrientes y promoviendo un funcionamiento corporal eficiente.\r\n\r\nMás que un simple suplemento, es una herramienta de bio-optimización que ayuda a regular los niveles de glucosa y combate la inflamación abdominal, permitiéndote sentirte ligero y con energía constante durante el día. Ideal para integrar en rituales de vida saludable y nutrición consciente.', 'Sugerencia de uso: Tomar 2 cápsulas al día, preferiblemente 20 minutos antes de la comida principal o el desayuno, acompañadas con un vaso de agua (250 ml).\r\n\r\nConsistencia: Para resultados óptimos en el equilibrio del pH y metabolismo, se recomienda su uso continuo durante al menos 90 días.', 'Apoyo en planes de control de peso y pérdida de grasa.\r\n\r\nProtocolos de desintoxicación (Detox) y limpieza hepática.\r\n\r\nAuxiliar en digestiones pesadas o lentas.\r\n\r\nSuplemento de apoyo para el control glucémico post-comidas.', 'Aceleración Metabólica: Optimiza la quema de energía y apoya la composición corporal saludable.\r\n\r\nControl de Saciedad: Ayuda a reducir los antojos y mejora la relación con las porciones de comida.\r\n\r\nBalance Glucémico: Contribuye a mantener niveles estables de azúcar en sangre.\r\n\r\nConfort Digestivo: Reduce significativamente la inflamación abdominal y los gases tras comidas copiosas.\r\n\r\nEfecto Depurativo: Facilita la eliminación de toxinas y mejora la salud de la microbiota intestinal.', '60 mg', '60 capsulas 500 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 05:26:07', '2026-04-23 05:26:07', NULL),
(2, 7, 2, 'CAMU MACU', 'camu-macu', 'SNS-CAM-MAC-500', 399.00, NULL, 'La fuente más potente de Vitamina C del planeta en tu rutina diaria. CAMU MACU es un concentrado de vitalidad diseñado para blindar tu sistema inmune, activar la producción natural de colágeno y combatir el envejecimiento celular desde la primera dosis.', 'Descubre el poder del Amazonas con CAMU MACU, un suplemento de alta densidad nutricional que redefine el concepto de antioxidante. Mientras que una naranja es conocida por su vitamina C, el Camu Camu ofrece una concentración hasta 40 veces mayor, convirtiéndolo en un escudo biológico incomparable.\r\n\r\nEste fitonutriente no solo fortalece tus defensas; actúa como un cofactor esencial en la síntesis de colágeno, mejorando la estructura de la piel y protegiendo los tejidos conectivos. Gracias a su perfil rico en flavonoides, CAMU MACU optimiza la energía celular y apoya la salud de órganos críticos como el hígado y la vista, ofreciendo una protección integral contra los radicales libres y el desgaste del estilo de vida moderno.', 'Dosis recomendada: Tomar 1 o 2 cápsulas diariamente, preferiblemente por la mañana con el desayuno para maximizar la absorción de nutrientes y los niveles de energía.\r\n\r\nNota de uso: Se recomienda evitar el consumo simultáneo con lácteos para no interferir con la biodisponibilidad de sus compuestos antioxidantes.', 'Refuerzo estacional del sistema inmunológico (prevención de gripes y virus).\r\n\r\nProtocolos de rejuvenecimiento y cuidado de la piel (Anti-aging).\r\n\r\nSuplementación para deportistas que buscan reducir el estrés oxidativo post-entrenamiento.\r\n\r\nApoyo nutricional en periodos de fatiga crónica o alta demanda intelectual.', 'Escudo Inmunológico: Maximiza la respuesta del cuerpo ante amenazas externas.\r\n\r\nBoost de Colágeno: Estimula de forma natural la firmeza de la piel y la salud articular.\r\n\r\nClaridad y Vitalidad: Reduce la fatiga metabólica y aumenta el enfoque diario.\r\n\r\nProtección Ocular y Hepática: Sus antocianinas ayudan a proteger la retina y purificar las funciones del hígado.\r\n\r\nAcción Anti-Edad: Neutraliza los radicales libres, retrasando el envejecimiento prematuro de los tejidos.', '60 mg', '60 capsulas 500 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 05:33:44', '2026-04-23 05:33:44', NULL),
(3, 5, 2, 'Luteína', 'luteina', 'SNS-LUT-EYE-20MG', 450.00, NULL, 'Tu filtro natural contra la era digital. La Luteína es un carotenoide esencial que actúa como un escudo protector para tus ojos, filtrando la luz azul de las pantallas y combatiendo el estrés oxidativo para mantener una visión nítida y saludable.', 'Protege tu activo más valioso con nuestra Luteína de Alta Pureza. En un mundo dominado por pantallas y exposición constante a la luz azul, tus ojos enfrentan un desgaste diario sin precedentes. Este suplemento actúa como un \"lente interno\", concentrándose de forma natural en la mácula y la retina para absorber las radiaciones dañinas y neutralizar los radicales libres.\r\n\r\nMás allá de la visión, la Luteína es un antioxidante sistémico que apoya la elasticidad de la piel y la integridad celular. Es la inversión inteligente para estudiantes, profesionales y adultos que buscan prevenir el deterioro visual prematuro y mantener la claridad en su mirada a largo plazo.', 'Dosis sugerida: Tomar 1 cápsula al día, preferiblemente con una comida que contenga grasas saludables (como aguacate, aceite de oliva o frutos secos) para maximizar la absorción de este carotenoide.\r\n\r\nUso recomendado: Ideal para consumo diario prolongado, especialmente en épocas de alta demanda visual.', 'Protección contra la fatiga visual por uso de computadoras y smartphones.\r\n\r\nPrevención del desgaste ocular relacionado con la edad.\r\n\r\nRefuerzo antioxidante para la salud dérmica.\r\n\r\nApoyo en la recuperación visual tras exposición prolongada al sol.', 'Filtro de Luz Azul: Minimiza el impacto negativo de las pantallas LED y dispositivos electrónicos.\r\n\r\nAgudeza Visual: Contribuye a una visión más definida y mejora la sensibilidad al contraste.\r\n\r\nDescanso Ocular: Reduce significativamente la irritación y la fatiga tras largas jornadas de trabajo.\r\n\r\nProtección Macular: Ayuda a preservar la densidad del pigmento macular, vital para la visión central.\r\n\r\nCuidado Anti-Edad: Combate el estrés oxidativo tanto en los ojos como en la piel.', '60 mg', '60 capsulas 500 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 05:37:39', '2026-04-23 05:37:39', NULL),
(4, 6, 2, 'HepaGold', 'hepagold', 'SNS-HEP-GLD-CURC', 425.00, NULL, 'El estándar de oro para la purificación de tu hígado. HepaGold combina la potencia antiinflamatoria de la cúrcuma con la activación de la pimienta negra para crear un sistema de desintoxicación profunda, eliminando toxinas y restaurando el equilibrio metabólico desde la raíz.', 'Restaura tu filtro vital con HepaGold, una fórmula avanzada diseñada para proteger y regenerar la función hepática. El hígado es el responsable de procesar cada toxina que ingresa a tu cuerpo; HepaGold le proporciona las herramientas necesarias para hacerlo de manera eficiente.\r\n\r\nUtilizando curcumina de alta pureza potenciada con piperina (pimienta negra), este suplemento aumenta la absorción del activo hasta en un 2000%, garantizando que cada dosis trabaje activamente en la reducción de la inflamación sistémica y la neutralización de radicales libres. Es el aliado indispensable para quienes buscan revertir el impacto de dietas pesadas, estrés ambiental o simplemente desean un metabolismo de grasas más ágil y saludable.', 'Dosis recomendada: Tomar 2 cápsulas al día, de preferencia con la comida más abundante del día para facilitar el procesamiento de lípidos y mejorar la absorción de la curcumina.\r\n\r\nSugerencia de uso: Para un protocolo \"Detox\" intensivo, mantener el consumo constante durante 60 días.', 'Protocolos de limpieza hepática y biliar.\r\n\r\nAuxiliar en la digestión de grasas y reducción de la pesadez estomacal.\r\n\r\nApoyo natural para reducir la inflamación articular y muscular.\r\n\r\nRecuperación metabólica tras periodos de excesos alimentarios.', 'Protección Hepática Activa: Crea un escudo antioxidante que protege las células del hígado (hepatocitos).\r\n\r\nBio-Absorción Potenciada: Máxima efectividad garantizada gracias a la sinergia con pimienta negra.\r\n\r\nMetabolismo de Grasas: Facilita la descomposición de lípidos, apoyando la salud digestiva y el control de peso.\r\n\r\nPotente Antiinflamatorio: Ayuda a reducir marcadores de inflamación en todo el organismo.\r\n\r\nDepuración Natural: Acelera la eliminación de toxinas y metales pesados acumulados.', '60 mg', '60 capsulas 500 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 05:41:37', '2026-04-23 05:41:37', NULL),
(5, 4, 2, 'CordyBoost', 'cordyboost', 'SNS-COR-BST-800', 430.00, NULL, 'Energía pura, sin cafeína ni efectos secundarios. CordyBoost es tu combustible biológico para superar tus límites. Gracias al poder adaptógeno del Cordyceps, optimiza la oxigenación celular, elevando tu resistencia física y claridad mental a un nuevo nivel de rendimiento consciente.', 'Rompe la barrera del agotamiento con CordyBoost. Diseñado para aquellos que viven a alta intensidad, este suplemento aprovecha las propiedades ancestrales del hongo Cordyceps para mejorar la producción de ATP (la moneda energética de tus células). A diferencia de los estimulantes convencionales que generan picos y caídas, CordyBoost proporciona una vitalidad sostenida al mejorar la eficiencia respiratoria y la oxigenación sanguínea.\r\n\r\nMás que un simple potenciador, actúa como un adaptógeno de élite: enseña a tu organismo a gestionar el estrés físico y mental de manera eficiente, fortaleciendo tu sistema inmune y permitiéndote mantener el enfoque incluso en jornadas de alta exigencia. Es el aliado definitivo para atletas, profesionales creativos y cualquier persona que busque rendir al máximo sin comprometer su equilibrio interno.', 'Dosis recomendada: Tomar 1 a 2 cápsulas diarias por la mañana o 30 minutos antes de tu sesión de entrenamiento.\r\n\r\nConsistencia: Para notar una mejora real en la resistencia física y el umbral de fatiga, se recomienda su uso diario por un periodo mínimo de 4 a 6 semanas.', 'Aumento de la capacidad aeróbica y resistencia en deportes de alta intensidad.\r\n\r\nRecuperación frente al agotamiento mental y \"niebla cerebral\" (brain fog).\r\n\r\nApoyo en protocolos de entrenamiento funcional o de fuerza.\r\n\r\nRefuerzo del sistema inmune ante el estrés ambiental y el sobreentrenamiento.', 'Potencia de ATP: Mejora la producción de energía celular directa, reduciendo la percepción de esfuerzo.\r\n\r\nOxigenación Optimizada: Favorece una mayor capacidad pulmonar y uso del oxígeno por los tejidos.\r\n\r\nResiliencia Adaptógena: Ayuda al cuerpo a equilibrar el cortisol y responder positivamente al estrés.\r\n\r\nEnfoque de Larga Duración: Claridad mental constante sin los \"jitter\" o nerviosismo de la cafeína.\r\n\r\nVitalidad Integral: Fortalece las defensas naturales y apoya la salud de órganos internos.', '60 mg', '60 capsulas 500 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 05:45:07', '2026-04-23 05:45:07', NULL),
(6, 6, 2, 'Pro-Flora', 'pro-flora', 'SNS-PRO-FLR-60C', 300.00, NULL, 'Restaura tu equilibrio desde el interior. Pro-Flora es una fórmula avanzada de 60 cápsulas diseñada para repoblar tu microbiota con cepas beneficiosas que fortalecen tus defensas naturales, optimizan la digestión y promueven una sensación de bienestar integral cada día.', 'Tu salud comienza en el intestino. Pro-Flora es un suplemento simbiótico de alta potencia que combina cepas probióticas seleccionadas con fibras prebióticas para garantizar la supervivencia y activación de las bacterias buenas en tu organismo. En un mundo donde la dieta moderna y el estrés debilitan nuestra flora intestinal, Pro-Flora actúa como un arquitecto de tu ecosistema interno.\r\n\r\nEsta fórmula no solo facilita una digestión ligera y combate la inflamación; el 70% de tu sistema inmunológico reside en el intestino, por lo que Pro-Flora es tu primera línea de defensa. Además, al apoyar el eje intestino-cerebro, contribuye a mejorar tu estado de ánimo y niveles de energía, permitiéndote vivir con ligereza y protección total.', 'Dosis recomendada: Tomar 1 cápsula al día, preferiblemente en ayunas o 30 minutos antes de una comida para asegurar que los probióticos lleguen intactos al tracto intestinal.\r\n\r\nConservación: Mantener el frasco en un lugar fresco y seco. No requiere refrigeración gracias a nuestra tecnología de estabilización de cepas.', 'Restauración de la flora intestinal tras el uso de antibióticos.\r\n\r\nApoyo en casos de colon irritable, gases o hinchazón abdominal frecuente.\r\n\r\nRefuerzo del sistema inmune en cambios de estación.\r\n\r\nMejora del tránsito intestinal y la absorción de nutrientes.', 'Inmunidad Potenciada: Estimula las defensas naturales del cuerpo desde su origen intestinal.\r\n\r\nBalance Digestivo: Reduce la pesadez y promueve evacuaciones regulares y saludables.\r\n\r\nControl de la Inflamación: Ayuda a mantener un vientre plano al reducir la fermentación bacteriana negativa.\r\n\r\nBarrera Protectora: Evita la colonización de patógenos dañinos en el sistema digestivo.\r\n\r\nBienestar Sistémico: Mejora la síntesis de vitaminas esenciales y apoya la salud emocional.', '60 mg', '60 capsulas 500 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(7, 6, 3, 'Inulina de Achicoria', 'inulina-de-achicoria', 'SNS-INU-ACH-250', 400.00, NULL, 'El alimento vital para tu microbiota. La Inulina de Achicoria es una fibra soluble prebiótica que nutre las bacterias benéficas de tu intestino, mejora la digestión y ayuda a mantener niveles saludables de azúcar y colesterol de forma 100% natural.', 'Optimiza tu salud digestiva con la pureza de la Inulina de Achicoria. Extraída de la raíz de la planta de achicoria (Cichorium intybus), esta fibra premium actúa como un fertilizante selectivo para tu microbiota, promoviendo el crecimiento de bífidobacterias esenciales para una salud óptima.\r\n\r\nA diferencia de otras fibras, la Inulina de Achicoria tiene un índice glucémico de casi cero y actúa como un modulador del apetito, ayudando a prolongar la sensación de saciedad. Su estructura molecular le permite viajar intacta hasta el colon, donde se fermenta para producir ácidos grasos de cadena corta, fundamentales para proteger la mucosa intestinal y fortalecer el sistema inmunológico desde la base.', 'Sugerencia de consumo: Mezclar 1 medida (incluida en el empaque o 5g) en agua, jugos, batidos o café. Al ser altamente soluble y tener un sabor neutro con un toque ligeramente dulce, puede añadirse a recetas de repostería saludable.\r\n\r\nNota: Se recomienda comenzar con media dosis durante la primera semana para permitir que el sistema digestivo se adapte gradualmente al incremento de fibra.', 'Sustituto de azúcar o grasa en recetas saludables (mejora la textura).\r\n\r\nSuplemento para el control de peso y reducción de picos de insulina.\r\n\r\nApoyo en dietas Keto o bajas en carbohidratos.\r\n\r\nTratamiento natural para el estreñimiento crónico o tránsito lento.', 'Prebiótico Avanzado: Alimenta exclusivamente a las bacterias buenas, mejorando el ecosistema intestinal.\r\n\r\nControl Glucémico: Ayuda a ralentizar la absorción de carbohidratos y azúcares.\r\n\r\nEfecto Saciante: Contribuye al control del peso al reducir el hambre entre comidas.\r\n\r\nAbsorción de Minerales: Favorece la absorción de calcio y magnesio en el tracto digestivo.\r\n\r\nTránsito Regular: Mejora la frecuencia y consistencia de las evacuaciones sin causar irritación.', '600 g', '', 0, NULL, 0.00, 0, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(8, 9, 2, 'CBF (Cellular Bio-Factor)', 'cbf-cellular-bio-factor', 'SNS-CBF-MAG-450', 504.00, NULL, 'El soporte esencial para tu circulación y vitalidad celular. CBF combina la alta biodisponibilidad del Citrato de Magnesio con una acción protectora integral que refuerza tus vasos sanguíneos, potencia la absorción de vitaminas y actúa como un potente escudo contra la inflamación y el estrés.', 'Optimiza la infraestructura de tu salud con CBF. Este suplemento ha sido formulado como un cofactor biológico crítico: no solo fortalece el sistema inmunológico, sino que actúa directamente sobre la salud vascular, mejorando la microcirculación y reforzando la integridad de los capilares.\r\n\r\nGracias a su base de Citrato de Magnesio de alta pureza (450 mg), CBF facilita procesos metabólicos clave, desde la formación de colágeno para una piel firme hasta la neutralización de radicales libres que causan el envejecimiento prematuro. Es el aliado perfecto para quienes buscan una respuesta natural ante alergias, pesadez en las piernas o simplemente desean maximizar la efectividad de sus otros suplementos, creando una sinergia perfecta en el organismo.', 'Dosis recomendada: Tomar 1 cápsula dos veces al día, preferiblemente con las comidas.\r\n\r\nSugerencia de uso: Para beneficios circulatorios (várices o piernas pesadas), se recomienda tomar una dosis por la tarde para favorecer la relajación muscular y el retorno venoso antes de descansar.', 'Mejora de la microcirculación y alivio de piernas cansadas o várices.\r\n\r\nCoadyuvante en el control de la presión arterial y salud cardiovascular.\r\n\r\nApoyo natural para personas con sensibilidad alérgica o rinitis.\r\n\r\nMaximización de la absorción de Vitamina C y otros antioxidantes.', 'Salud Vascular Avanzada: Refuerza las paredes sanguíneas y previene la fragilidad de los capilares.\r\n\r\nPotenciador de Nutrientes: Eleva la biodisponibilidad de activos como la Vitamina C y el Ácido Alfa Lipoico.\r\n\r\nEscudo Antioxidante: Neutraliza radicales libres, protegiendo las células del desgaste oxidativo.\r\n\r\nEfecto Antihistamínico: Ayuda a mitigar los síntomas de alergias y problemas respiratorios leves.\r\n\r\nAlivio de la Inflamación: Reduce la hinchazón y mejora la respuesta del cuerpo ante procesos crónicos.', '60 mg', '60 capsulas 450 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 06:06:16', '2026-04-23 06:06:16', NULL),
(9, 10, 2, 'ALA (Universal Metabolic Shield)', 'ala-universal-metabolic-shield', 'SNS-ALA-MET-480', 504.00, NULL, 'El antioxidante universal para una protección celular total. ALA es un potente regulador metabólico que optimiza el uso de la glucosa, protege el sistema nervioso y regenera otros antioxidantes en tu cuerpo, asegurando que cada célula funcione con su máxima energía y vitalidad.', 'Redefine tu salud celular con ALA de SENSËA. El Ácido Alfa Lipoico es un compuesto extraordinario capaz de actuar tanto en medios acuosos como grasos, lo que le permite proteger cada rincón de tu organismo, desde el cerebro hasta el hígado.\r\n\r\nEsta fórmula de 480 mg por cápsula es un aliado crítico para el equilibrio glucémico, mejorando la sensibilidad a la insulina y facilitando la conversión de azúcar en energía utilizable. Además de su papel metabólico, el ALA es un neuroprotector de élite que resguarda las terminaciones nerviosas y potencia la regeneración de las vitaminas C y E, creando un ciclo de protección antioxidante infinito que combate el envejecimiento prematuro y la inflamación silenciosa.', 'Dosis recomendada: Tomar 1 cápsula al día, preferiblemente con el estómago vacío (30-60 minutos antes de comer) para maximizar su absorción y eficacia metabólica.\r\n\r\nSugerencia de uso: Para apoyo en niveles de glucosa o rendimiento energético, mantener un consumo constante diario.', 'Control y equilibrio de los niveles de azúcar en sangre (resistencia a la insulina).\r\n\r\nProtección del sistema nervioso y apoyo en casos de neuropatías.\r\n\r\nProtocolos de desintoxicación de metales pesados y salud hepática.\r\n\r\nSuplemento antienvejecimiento para la protección de las mitocondrias.', 'Control Glucémico Inteligente: Mejora la respuesta del cuerpo a la insulina y regula la glucosa.\r\n\r\nAntioxidante de Rango Completo: Neutraliza radicales libres en todas las células del cuerpo.\r\n\r\nNeuroprotección Avanzada: Resguarda la salud de los nervios y apoya la claridad cognitiva.\r\n\r\nReciclaje Antioxidante: Regenera activamente la Vitamina C, E y el Glutatión en el organismo.\r\n\r\nImpulso Mitocondrial: Fortalece la producción de energía celular, reduciendo la fatiga crónica.', '60 mg', '60 capsulas 480 mg c/u', 0, NULL, 0.00, 0, '2026-04-23 06:14:56', '2026-04-23 06:14:56', NULL),
(10, 8, 1, 'Gel Conductor & Reductor (Sculpt & Repair Formula)', 'gel-conductor-reductor-sculpt-repair-formula', 'SNS-GEL-CEN-500', 472.00, NULL, 'Esculpe, reafirma y regenera. Este gel avanzado con Centella Asiática y un $7\\%$ de Ácido Hialurónico es el aliado definitivo para mejorar la arquitectura de tu piel. Diseñado para combatir la flacidez y la celulitis, su fórmula biocompatible hidrata profundamente mientras estimula la circulacion.', 'Lleva tu rutina de cuidado corporal al siguiente nivel con el Gel Conductor & Reductor SENSËA. Esta fórmula técnica ha sido desarrollada para actuar en las zonas más exigentes como abdomen, glúteos y brazos. La sinergia entre la Centella Asiática —un potente regenerador de tejidos— y una concentración excepcional de Ácido Hialurónico al $7\\%$, permite que la piel recupere su elasticidad y firmeza natural.Su textura ligera y de rápida absorción no solo mejora la apariencia de la \"piel de naranja\", sino que también sirve como un conductor profesional para masajes, uso con fajas o aparatología estética. Al ser un producto biocompatible (no irritante), garantiza una piel lisa, uniforme y profundamente hidratada sin dejar residuos grasos, proporcionando un alivio refrescante tras cada aplicación.', 'Aplicación diaria: Aplicar sobre la piel limpia y seca mediante masajes circulares ascendentes en la zona deseada hasta su completa absorción.\r\n\r\nUso profesional: Ideal como gel conductor para tratamientos con aparatología, envolturas, vendas frías/calientes o para uso bajo fajas modeladoras para potenciar el efecto térmico.', 'Tratamiento intensivo contra la celulitis y flacidez.\r\n\r\nGel conductor para masajes reductores y reafirmantes.\r\n\r\nCuidado post-ejercicio para refrescar y tonificar los tejidos.\r\n\r\nHidratación intensiva para pieles que han perdido firmeza por cambios de peso.', 'Máxima Firmeza: Estimula la producción de colágeno gracias a la Centella Asiática.Hidratación Ultra-Profunda: Su $7\\%$ de Ácido Hialurónico rellena y suaviza la textura de la piel.Efecto Drenante: Favorece la microcirculación, reduciendo la retención de líquidos y hoyuelos.Biocompatibilidad Certificada: Fórmula segura, no irritante y apta para todo tipo de piel (pH 6-8).Absorción Instantánea: Deja la piel fresca y tersa sin sensación pegajosa.', '500 ml', '', 0, NULL, 0.00, 0, '2026-04-23 06:19:49', '2026-04-23 06:19:49', NULL),
(11, 8, 1, 'Serum Facial Antiedad', 'serum-facial-antiedad', 'SNS-SRM-AGE-50ML', 359.00, NULL, 'El elixir definitivo para una piel radiante y rejuvenecida. Este Serum Facial Antiedad combina la potencia de la Vitamina C, la Niacinamida y el activo marino Marsturizer™ para iluminar, reafirmar y regenerar tu rostro. Una fórmula multicapa que combate las arrugas mientras restaura la piel.', 'Transforma la textura de tu piel con el Serum Facial Antiedad SENSËA, una obra maestra de la dermocosmética que reúne los ingredientes más potentes de la naturaleza y la ciencia. Gracias a su base de Ácido Hialurónico y Marsturizer™, este suero proporciona una hidratación profunda que \"rellena\" las líneas de expresión desde el interior.\r\n\r\nLa sinergia entre el Colágeno, la Elastina y la Vitamina C estimula la arquitectura dérmica, devolviendo la firmeza y elasticidad perdidas por el paso del tiempo. Además, el Óleo de Rosa Mosqueta y la Niacinamida trabajan en conjunto para unificar el tono de la piel, aclarando manchas y protegiendo tu rostro del daño oxidativo causado por el sol y la contaminación. Su textura sedosa de rápida absorción penetra en las capas más profundas, dejando un acabado luminoso, terso y libre de sensación grasa.', 'Ritual de aplicación: Aplicar 3 a 5 gotas sobre el rostro, cuello y escote limpios. Distribuir suavemente con las yemas de los dedos mediante toques ligeros (tecleo) hasta su completa absorción.\r\n\r\nFrecuencia: Uso diario, mañana y noche. Por la mañana, se recomienda aplicar protector solar después del serum para proteger la piel y potenciar el efecto de la Vitamina C.', 'Tratamiento correctivo para líneas de expresión y arrugas profundas.\r\n\r\nProtocolo iluminador para pieles opacas o con fatiga urbana.\r\n\r\nRegeneración de pieles con marcas de acné o cicatrices leves (gracias a la Rosa Mosqueta).\r\n\r\nPrevención del fotoenvejecimiento y protección antioxidante diaria.', 'Efecto de Relleno Inmediato: El Ácido Hialurónico retiene la humedad para una piel visiblemente más densa.\r\n\r\nLuminosidad y Tono Uniforme: La Vitamina C y la Niacinamida actúan como potentes despigmentantes y revitalizadores.\r\n\r\nArquitectura Dérmica: Fortalece la estructura de la piel gracias al aporte directo de Colágeno y Elastina.\r\n\r\nRegeneración Celular: El activo Marsturizer™ y la Rosa Mosqueta aceleran la reparación de tejidos dañados.\r\n\r\nDefensa Antioxidante: Escudo activo contra radicales libres y estrés ambiental.', '50 ml', '', 0, NULL, 0.00, 0, '2026-04-23 06:24:48', '2026-04-23 06:24:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_etiquetas`
--

CREATE TABLE `productos_etiquetas` (
  `producto_id` int(10) UNSIGNED NOT NULL,
  `etiqueta_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos_etiquetas`
--

INSERT INTO `productos_etiquetas` (`producto_id`, `etiqueta_id`) VALUES
(1, 19),
(1, 21),
(2, 25),
(2, 26),
(2, 28),
(2, 29),
(3, 14),
(3, 15),
(3, 28),
(4, 8),
(4, 20),
(4, 35),
(5, 6),
(5, 7),
(6, 18),
(6, 23),
(6, 25),
(7, 18),
(8, 8),
(8, 12),
(8, 25),
(8, 35),
(9, 25),
(9, 34),
(10, 33),
(11, 8),
(11, 25),
(11, 28),
(11, 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_imagenes`
--

CREATE TABLE `productos_imagenes` (
  `id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `url_imagen` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos_imagenes`
--

INSERT INTO `productos_imagenes` (`id`, `producto_id`, `url_imagen`, `alt_text`, `es_principal`, `orden`, `created_at`, `updated_at`, `deleted_at`) VALUES
(17, 1, '1_f06fdbb88f2622e197a58595090d2c10.png', NULL, 1, 0, '2026-04-23 05:26:07', '2026-04-23 05:26:07', NULL),
(18, 2, '2_5761e222e8e7b63d9db12f3f76566b45.png', NULL, 1, 0, '2026-04-23 05:33:44', '2026-04-23 05:33:44', NULL),
(19, 3, '3_012da01b1eafa55bfd85640679f2ecd7.png', NULL, 1, 0, '2026-04-23 05:37:39', '2026-04-23 05:37:39', NULL),
(20, 4, '4_4acada41f946785ce578835c232eb423.png', NULL, 1, 0, '2026-04-23 05:41:37', '2026-04-23 05:41:37', NULL),
(21, 5, '5_8a707c2c77c4e94e09f5f140c8b37d85.png', NULL, 1, 0, '2026-04-23 05:45:07', '2026-04-23 05:45:07', NULL),
(22, 6, '6_036057253d3969348b9464ea2adb8e41.jpg', NULL, 1, 0, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(23, 6, '6_3b47d1c6069fc7847002dd113c439346.jpg', NULL, 0, 1, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(24, 6, '6_9e8f96a1cf3f2cd481b6b8074216f483.jpg', NULL, 0, 2, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(25, 6, '6_1fb2cdb4be13b3d751eff5594e47ab7e.jpg', NULL, 0, 3, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(26, 6, '6_1f8a03b026a2371b42a191ed2c107ea7.jpg', NULL, 0, 4, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(27, 6, '6_c5969fce4b1448bbc4dbfa94a493c78e.jpg', NULL, 0, 5, '2026-04-23 05:51:41', '2026-04-23 05:51:41', NULL),
(28, 7, '7_34b6f8b52670daeec997bcbe9c9ec997.jpg', NULL, 1, 0, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(29, 7, '7_fee0148b24e5b62554089df7fcfd7daa.jpg', NULL, 0, 1, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(30, 7, '7_387354f94c2c94413d8e98c92302aa8e.jpg', NULL, 0, 2, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(31, 7, '7_803ff848b6a484f5769425d85547286e.jpg', NULL, 0, 3, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(32, 7, '7_8a72253beda5edb05bf5cc24b9825f3d.jpg', NULL, 0, 4, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(33, 7, '7_a294381fbfb4d726185f83e23769c672.jpg', NULL, 0, 5, '2026-04-23 06:00:00', '2026-04-23 06:00:00', NULL),
(34, 8, '8_30e5077c91b10b16c5d4bc75594eb17f.jpg', NULL, 1, 0, '2026-04-23 06:06:16', '2026-04-23 06:06:16', NULL),
(35, 9, '9_a92412dd8727afd9f8bd36ba8c7009cd.jpg', NULL, 1, 0, '2026-04-23 06:14:56', '2026-04-23 06:14:56', NULL),
(36, 10, '10_f755750fa71acd205a77629c720c130a.jpg', NULL, 1, 0, '2026-04-23 06:19:49', '2026-04-23 06:19:49', NULL),
(37, 11, '11_fc4c0f79ed96cbbe865c9ecc96636ca2.jpg', NULL, 1, 0, '2026-04-23 06:24:48', '2026-04-23 06:24:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `calificacion` tinyint(3) UNSIGNED NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `estatus` enum('publicada','eliminada') NOT NULL DEFAULT 'publicada',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `resenas`
--
DELIMITER $$
CREATE TRIGGER `trg_resenas_after_delete` AFTER DELETE ON `resenas` FOR EACH ROW BEGIN
  CALL `sp_recalcular_resenas_producto`(OLD.producto_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_resenas_after_insert` AFTER INSERT ON `resenas` FOR EACH ROW BEGIN
  CALL `sp_recalcular_resenas_producto`(NEW.producto_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_resenas_after_update` AFTER UPDATE ON `resenas` FOR EACH ROW BEGIN
  IF OLD.producto_id <> NEW.producto_id THEN
    CALL `sp_recalcular_resenas_producto`(OLD.producto_id);
  END IF;
  CALL `sp_recalcular_resenas_producto`(NEW.producto_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_resenas_verificar_compra` BEFORE INSERT ON `resenas` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('cliente','admin','superadmin') NOT NULL DEFAULT 'cliente',
  `telefono` varchar(10) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `role`, `telefono`, `password_hash`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'Oscar', 'Sylva', 'oscar@gmail.com', 'cliente', NULL, '$2y$10$vepM.bRRVt8fBv8g0bdUjekOpeg28okNdjTB4XgMF6/dhFsPJO9a2', '2026-04-20 03:35:53', '2026-04-20 03:35:53', NULL),
(4, 'Jhonny', 'Joestar', 'jhony@gmail.com', 'admin', NULL, '$2y$10$Xuf7Y.IyNyhmxZKdbfjjcuX2KXjjKFrmT//w.c53IW2kPvxM3dyke', '2026-04-20 06:59:48', '2026-04-20 07:00:43', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_carritos_cliente_estado` (`cliente_id`,`estado`),
  ADD KEY `idx_carritos_session_id` (`session_id`),
  ADD KEY `idx_carritos_session_cliente` (`session_id`,`cliente_id`);

--
-- Indices de la tabla `carritos_items`
--
ALTER TABLE `carritos_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carritos_items_producto` (`carrito_id`,`producto_id`),
  ADD KEY `idx_carritos_items_producto` (`producto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categorias_nombre` (`nombre`),
  ADD UNIQUE KEY `uq_categorias_slug` (`slug`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_direcciones_cliente` (`cliente_id`),
  ADD KEY `idx_direcciones_cliente_principal` (`cliente_id`,`es_principal`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_etiquetas_nombre` (`nombre`),
  ADD UNIQUE KEY `uq_etiquetas_slug` (`slug`);

--
-- Indices de la tabla `formas_presentacion`
--
ALTER TABLE `formas_presentacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_formas_presentacion_nombre` (`nombre`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_metodos_pago_cliente` (`cliente_id`),
  ADD KEY `idx_metodos_pago_cliente_activo` (`cliente_id`,`activo`),
  ADD KEY `idx_metodos_pago_cliente_default` (`cliente_id`,`es_predeterminado`),
  ADD KEY `idx_metodos_pago_customer` (`customer_id`),
  ADD KEY `idx_metodos_pago_payment_method` (`payment_method_id`);

--
-- Indices de la tabla `pagos_transacciones`
--
ALTER TABLE `pagos_transacciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pagos_transacciones_referencia` (`referencia`),
  ADD KEY `idx_pagos_transacciones_pedido` (`pedido_id`),
  ADD KEY `idx_pagos_transacciones_cliente` (`cliente_id`),
  ADD KEY `idx_pagos_transacciones_metodo` (`metodo_pago_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pedidos_numero_pedido` (`numero_pedido`),
  ADD KEY `idx_pedidos_cliente` (`cliente_id`),
  ADD KEY `idx_pedidos_direccion` (`direccion_envio_id`),
  ADD KEY `idx_pedidos_metodo_pago` (`metodo_pago_id`),
  ADD KEY `idx_pedidos_estado_created_at` (`estado_pedido`,`created_at`),
  ADD KEY `idx_pedidos_cliente_estado` (`cliente_id`,`estado_pedido`),
  ADD KEY `idx_pedidos_mp_payment` (`mp_payment_id`);

--
-- Indices de la tabla `pedidos_detalle`
--
ALTER TABLE `pedidos_detalle`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pedidos_detalle_producto` (`pedido_id`,`producto_id`),
  ADD KEY `idx_pedidos_detalle_producto` (`producto_id`);

--
-- Indices de la tabla `pedidos_historial_estados`
--
ALTER TABLE `pedidos_historial_estados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedidos_historial_pedido_fecha` (`pedido_id`,`fecha_cambio`),
  ADD KEY `idx_pedidos_historial_usuario` (`cambiado_por_usuario_id`);

--
-- Indices de la tabla `pedidos_secuencia`
--
ALTER TABLE `pedidos_secuencia`
  ADD PRIMARY KEY (`fecha`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_productos_slug` (`slug`),
  ADD UNIQUE KEY `uq_productos_sku` (`sku`),
  ADD KEY `idx_productos_forma` (`forma_id`),
  ADD KEY `idx_productos_categoria_estatus` (`categoria_id`,`estatus`,`deleted_at`),
  ADD KEY `idx_productos_estatus_deleted` (`estatus`,`deleted_at`),
  ADD KEY `idx_productos_destacado` (`destacado`,`estatus`);

--
-- Indices de la tabla `productos_etiquetas`
--
ALTER TABLE `productos_etiquetas`
  ADD PRIMARY KEY (`producto_id`,`etiqueta_id`),
  ADD KEY `idx_productos_etiquetas_etiqueta` (`etiqueta_id`);

--
-- Indices de la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_productos_imagenes_orden` (`producto_id`,`orden`),
  ADD KEY `idx_productos_imagenes_principal` (`producto_id`,`es_principal`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_resenas_cliente_producto` (`cliente_id`,`producto_id`),
  ADD KEY `idx_resenas_producto_estatus` (`producto_id`,`estatus`),
  ADD KEY `idx_resenas_cliente` (`cliente_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carritos`
--
ALTER TABLE `carritos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carritos_items`
--
ALTER TABLE `carritos_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `formas_presentacion`
--
ALTER TABLE `formas_presentacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pagos_transacciones`
--
ALTER TABLE `pagos_transacciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos_detalle`
--
ALTER TABLE `pedidos_detalle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos_historial_estados`
--
ALTER TABLE `pedidos_historial_estados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD CONSTRAINT `fk_carritos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `carritos_items`
--
ALTER TABLE `carritos_items`
  ADD CONSTRAINT `fk_carritos_items_carrito` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carritos_items_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `fk_direcciones_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD CONSTRAINT `fk_metodos_pago_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos_transacciones`
--
ALTER TABLE `pagos_transacciones`
  ADD CONSTRAINT `fk_pagos_transacciones_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pagos_transacciones_metodo` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pagos_transacciones_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_direccion` FOREIGN KEY (`direccion_envio_id`) REFERENCES `direcciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_metodo_pago` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos_detalle`
--
ALTER TABLE `pedidos_detalle`
  ADD CONSTRAINT `fk_pedidos_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos_historial_estados`
--
ALTER TABLE `pedidos_historial_estados`
  ADD CONSTRAINT `fk_pedidos_historial_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_historial_usuario` FOREIGN KEY (`cambiado_por_usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_productos_forma` FOREIGN KEY (`forma_id`) REFERENCES `formas_presentacion` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_etiquetas`
--
ALTER TABLE `productos_etiquetas`
  ADD CONSTRAINT `fk_productos_etiquetas_etiqueta` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_productos_etiquetas_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_imagenes`
--
ALTER TABLE `productos_imagenes`
  ADD CONSTRAINT `fk_productos_imagenes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `fk_resenas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resenas_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
