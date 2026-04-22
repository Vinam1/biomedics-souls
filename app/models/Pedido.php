<?php

class Pedido
{
    public static function createDirect(array $user, array $address, array $paymentMethod, array $cartItems, array $paymentResult, float $shippingCost = 0.0): int
    {
        $db = Database::getInstance();
        $subtotal = 0.0;

        foreach ($cartItems as $item) {
            $subtotal += (float) $item['subtotal'];
        }

        $total = $subtotal + $shippingCost;
        $status = ($paymentResult['status'] ?? 'approved') === 'approved' ? 'pagado' : 'pendiente';
        $paidAt = $status === 'pagado' ? date('Y-m-d H:i:s') : null;
        $numeroPedido = $paymentResult['order_number'] ?? self::generateOrderNumber();

        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                'INSERT INTO pedidos (
                    numero_pedido, cliente_id, direccion_envio_id, metodo_pago_id, estado_pedido,
                    subtotal, costo_envio, total, moneda,
                    mp_preference_id, mp_payment_id, mp_status, mp_status_detail, pagado_at,
                    direccion_nombre_completo, direccion_telefono, direccion_calle,
                    direccion_numero_exterior, direccion_numero_interior, direccion_colonia,
                    direccion_ciudad, direccion_estado, direccion_pais, direccion_codigo_postal,
                    direccion_referencias
                ) VALUES (
                    :numero_pedido, :cliente_id, :direccion_envio_id, :metodo_pago_id, :estado_pedido,
                    :subtotal, :costo_envio, :total, :moneda,
                    :mp_preference_id, :mp_payment_id, :mp_status, :mp_status_detail, :pagado_at,
                    :direccion_nombre_completo, :direccion_telefono, :direccion_calle,
                    :direccion_numero_exterior, :direccion_numero_interior, :direccion_colonia,
                    :direccion_ciudad, :direccion_estado, :direccion_pais, :direccion_codigo_postal,
                    :direccion_referencias
                )'
            );

            $stmt->execute([
                'numero_pedido' => $numeroPedido,
                'cliente_id' => (int) $user['id'],
                'direccion_envio_id' => (int) $address['id'],
                'metodo_pago_id' => (int) $paymentMethod['id'],
                'estado_pedido' => $status,
                'subtotal' => $subtotal,
                'costo_envio' => $shippingCost,
                'total' => $total,
                'moneda' => $paymentResult['currency'] ?? 'MXN',
                'mp_preference_id' => $paymentResult['preference_id'] ?? null,
                'mp_payment_id' => $paymentResult['transaction_id'] ?? null,
                'mp_status' => $paymentResult['status'] ?? null,
                'mp_status_detail' => $paymentResult['detail'] ?? null,
                'pagado_at' => $paidAt,
                'direccion_nombre_completo' => trim(($user['nombre'] ?? '') . ' ' . ($user['apellidos'] ?? '')),
                'direccion_telefono' => $user['telefono'] ?? null,
                'direccion_calle' => $address['calle'] ?? null,
                'direccion_numero_exterior' => $address['numero_exterior'] ?? null,
                'direccion_numero_interior' => $address['numero_interior'] ?? null,
                'direccion_colonia' => $address['colonia'] ?? null,
                'direccion_ciudad' => $address['ciudad'] ?? null,
                'direccion_estado' => $address['estado'] ?? null,
                'direccion_pais' => $address['pais'] ?? null,
                'direccion_codigo_postal' => $address['codigo_postal'] ?? null,
                'direccion_referencias' => $address['referencias'] ?? null,
            ]);

            $orderId = (int) $db->lastInsertId();
            $detailStmt = $db->prepare(
                'INSERT INTO pedidos_detalle (
                    pedido_id, producto_id, producto_nombre, producto_sku, cantidad,
                    precio_original, precio_descuento, precio_unitario, subtotal
                ) VALUES (
                    :pedido_id, :producto_id, :producto_nombre, :producto_sku, :cantidad,
                    :precio_original, :precio_descuento, :precio_unitario, :subtotal
                )'
            );

            foreach ($cartItems as $item) {
                $product = $item['product'];
                $price = (float) ($product['precio_descuento'] ?? $product['precio']);

                $detailStmt->execute([
                    'pedido_id' => $orderId,
                    'producto_id' => (int) $product['id'],
                    'producto_nombre' => $product['nombre'],
                    'producto_sku' => $product['sku'],
                    'cantidad' => (int) $item['quantity'],
                    'precio_original' => (float) $product['precio'],
                    'precio_descuento' => isset($product['precio_descuento']) ? (float) $product['precio_descuento'] : null,
                    'precio_unitario' => $price,
                    'subtotal' => (float) $item['subtotal'],
                ]);
            }

            PagoTransaccion::create($orderId, (int) $user['id'], (int) $paymentMethod['id'], $paymentResult, $total);
            $db->commit();

            return $orderId;
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }
    }

    public static function countAll(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT COUNT(*) AS total FROM pedidos WHERE deleted_at IS NULL');
        $row = $stmt->fetch();
        return $row ? (int) $row['total'] : 0;
    }

    /**
     * Contar pedidos de un mes específico (formato YYYY-MM)
     */
    public static function countByMonth(string $yearMonth): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) AS total 
                              FROM pedidos 
                              WHERE DATE_FORMAT(created_at, '%Y-%m') = :month 
                                AND deleted_at IS NULL");
        $stmt->execute(['month' => $yearMonth]);
        $row = $stmt->fetch();
        return $row ? (int) $row['total'] : 0;
    }

    /**
     * Ventas totales de un mes específico
     */
    public static function totalSalesByMonth(string $yearMonth): float
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COALESCE(SUM(total), 0) AS total 
                              FROM pedidos 
                              WHERE DATE_FORMAT(created_at, '%Y-%m') = :month 
                                AND deleted_at IS NULL 
                                AND estado_pedido NOT IN ('cancelado')");
        $stmt->execute(['month' => $yearMonth]);
        $row = $stmt->fetch();
        return $row ? (float) $row['total'] : 0.0;
    }

    /**
     * Ventas totales históricas (todos los pedidos no cancelados)
     */
    public static function totalSales(): float
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COALESCE(SUM(total), 0) AS total 
                            FROM pedidos 
                            WHERE deleted_at IS NULL 
                              AND estado_pedido NOT IN ('cancelado')");
        $row = $stmt->fetch();
        return $row ? (float) $row['total'] : 0.0;
    }

    /**
     * Obtener pedidos por estado (pendiente, entregado, etc.)
     */
    public static function getByStatus(string $status, int $limit = 6): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.id, p.numero_pedido, p.total, p.created_at, p.estado_pedido,
                                     u.nombre AS cliente_nombre 
                              FROM pedidos p 
                              LEFT JOIN usuarios u ON u.id = p.cliente_id 
                              WHERE p.estado_pedido = :status 
                                AND p.deleted_at IS NULL 
                              ORDER BY p.created_at DESC 
                              LIMIT :limit");
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function recent(int $limit = 5): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.numero_pedido, p.estado_pedido, p.total, p.created_at,
                    u.nombre AS cliente_nombre
             FROM pedidos p
             LEFT JOIN usuarios u ON u.id = p.cliente_id
             WHERE p.deleted_at IS NULL
             ORDER BY p.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT p.id, p.numero_pedido, p.estado_pedido, p.total, p.created_at,
                    u.nombre AS cliente_nombre,
                    COUNT(pd.id) AS item_count
             FROM pedidos p
             LEFT JOIN usuarios u ON u.id = p.cliente_id
             LEFT JOIN pedidos_detalle pd ON pd.pedido_id = p.id
             WHERE p.deleted_at IS NULL
             GROUP BY p.id
             ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.numero_pedido, p.estado_pedido, p.total, p.subtotal, p.costo_envio, p.created_at,
                    p.direccion_nombre_completo, p.direccion_telefono, p.direccion_calle,
                    p.direccion_numero_exterior, p.direccion_numero_interior, p.direccion_colonia,
                    p.direccion_ciudad, p.direccion_estado, p.direccion_pais, p.direccion_codigo_postal,
                    p.direccion_referencias,
                    u.nombre AS cliente_nombre, u.apellidos AS cliente_apellidos, u.email AS cliente_email,
                    mp.tipo AS tipo_metodo_pago, mp.brand AS brand_metodo_pago, mp.ultimo_cuatro
             FROM pedidos p
             LEFT JOIN usuarios u ON u.id = p.cliente_id
             LEFT JOIN metodos_pago mp ON mp.id = p.metodo_pago_id
             WHERE p.id = :id AND p.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function items(int $orderId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT pd.producto_nombre, pd.producto_sku, pd.cantidad, pd.precio_unitario, pd.subtotal
             FROM pedidos_detalle pd
             WHERE pd.pedido_id = :orderId'
        );
        $stmt->execute(['orderId' => $orderId]);
        return $stmt->fetchAll();
    }

    public static function findByClienteId(int $clienteId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.numero_pedido, p.estado_pedido, p.total, p.created_at,
                    COUNT(pd.id) as item_count
             FROM pedidos p
             LEFT JOIN pedidos_detalle pd ON pd.pedido_id = p.id
             WHERE p.cliente_id = :clienteId AND p.deleted_at IS NULL
             GROUP BY p.id
             ORDER BY p.created_at DESC'
        );
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function findByIdForCliente(int $id, int $clienteId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.*, u.nombre AS cliente_nombre, u.apellidos AS cliente_apellidos, u.email AS cliente_email,
                    mp.tipo AS tipo_metodo_pago, mp.brand AS brand_metodo_pago, mp.ultimo_cuatro
             FROM pedidos p
             INNER JOIN usuarios u ON u.id = p.cliente_id
             LEFT JOIN metodos_pago mp ON mp.id = p.metodo_pago_id
             WHERE p.id = :id AND p.cliente_id = :cliente_id AND p.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'cliente_id' => $clienteId,
        ]);

        return $stmt->fetch() ?: null;
    }

    public static function generateOrderNumber(): string
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');

        $stmt = $db->prepare(
            'INSERT INTO pedidos_secuencia (fecha, secuencia)
             VALUES (:fecha, 1)
             ON DUPLICATE KEY UPDATE secuencia = secuencia + 1'
        );
        $stmt->execute(['fecha' => $today]);

        $sequenceStmt = $db->prepare('SELECT secuencia FROM pedidos_secuencia WHERE fecha = :fecha LIMIT 1');
        $sequenceStmt->execute(['fecha' => $today]);
        $sequence = (int) $sequenceStmt->fetchColumn();

        return sprintf('BS-%s-%04d', date('Ymd'), $sequence);
    }
}
