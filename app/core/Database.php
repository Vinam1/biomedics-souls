<?php

class Database
{
    private static ?PDO $instance = null;
    private static bool $schemaEnsured = false;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                die("Error al conectar con la base de datos. Por favor, intente más tarde.");
            }
        }

        if (!self::$schemaEnsured) {
            self::ensureSchema(self::$instance);
            self::$schemaEnsured = true;
        }

        return self::$instance;
    }

    private static function ensureSchema(PDO $db): void
    {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS pagos_transacciones (
                id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                pedido_id int(10) UNSIGNED NOT NULL,
                cliente_id int(10) UNSIGNED NOT NULL,
                metodo_pago_id int(10) UNSIGNED DEFAULT NULL,
                gateway varchar(50) NOT NULL,
                referencia varchar(100) NOT NULL,
                estado varchar(50) NOT NULL,
                detalle varchar(255) DEFAULT NULL,
                monto decimal(10,2) NOT NULL,
                moneda char(3) NOT NULL DEFAULT 'MXN',
                payload_respuesta longtext DEFAULT NULL,
                created_at timestamp NOT NULL DEFAULT current_timestamp(),
                updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (id),
                UNIQUE KEY uq_pagos_transacciones_referencia (referencia),
                KEY idx_pagos_transacciones_pedido (pedido_id),
                KEY idx_pagos_transacciones_cliente (cliente_id),
                KEY idx_pagos_transacciones_metodo (metodo_pago_id),
                CONSTRAINT fk_pagos_transacciones_pedido
                    FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_pagos_transacciones_cliente
                    FOREIGN KEY (cliente_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_pagos_transacciones_metodo
                    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago (id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
