<?php

class MetodoPago
{
    public static function allByClienteId(int $clienteId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM metodos_pago WHERE cliente_id = :cliente_id AND deleted_at IS NULL AND activo = 1 ORDER BY es_predeterminado DESC, updated_at DESC');
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function findByIdForCliente(int $id, int $clienteId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM metodos_pago WHERE id = :id AND cliente_id = :cliente_id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id, 'cliente_id' => $clienteId]);
        return $stmt->fetch() ?: null;
    }

    public static function defaultForCliente(int $clienteId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM metodos_pago WHERE cliente_id = :cliente_id AND deleted_at IS NULL AND activo = 1 ORDER BY es_predeterminado DESC, updated_at DESC LIMIT 1');
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $clienteId, array $data): int
    {
        $db = Database::getInstance();
        if (!empty($data['es_predeterminado'])) {
            self::clearDefault($clienteId);
        }

        $stmt = $db->prepare('INSERT INTO metodos_pago (cliente_id, tipo, brand, ultimo_cuatro, tipo_tarjeta, token, payment_method_id, customer_id, nickname, es_predeterminado, activo) VALUES (:cliente_id, :tipo, :brand, :ultimo_cuatro, :tipo_tarjeta, :token, :payment_method_id, :customer_id, :nickname, :es_predeterminado, 1)');
        $stmt->execute([
            'cliente_id' => $clienteId,
            'tipo' => $data['tipo'],
            'brand' => $data['brand'] !== '' ? $data['brand'] : null,
            'ultimo_cuatro' => $data['ultimo_cuatro'] !== '' ? $data['ultimo_cuatro'] : null,
            'tipo_tarjeta' => $data['tipo_tarjeta'] !== '' ? $data['tipo_tarjeta'] : null,
            'token' => self::buildToken($data),
            'payment_method_id' => self::buildPaymentMethodId($data),
            'customer_id' => self::buildCustomerId($clienteId),
            'nickname' => $data['nickname'] !== '' ? $data['nickname'] : null,
            'es_predeterminado' => !empty($data['es_predeterminado']) ? 1 : 0,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, int $clienteId, array $data): bool
    {
        $db = Database::getInstance();
        if (!empty($data['es_predeterminado'])) {
            self::clearDefault($clienteId, $id);
        }

        $existing = self::findByIdForCliente($id, $clienteId);
        if (!$existing) {
            return false;
        }

        $stmt = $db->prepare('UPDATE metodos_pago SET tipo = :tipo, brand = :brand, ultimo_cuatro = :ultimo_cuatro, tipo_tarjeta = :tipo_tarjeta, token = :token, payment_method_id = :payment_method_id, customer_id = :customer_id, nickname = :nickname, es_predeterminado = :es_predeterminado, activo = :activo WHERE id = :id AND cliente_id = :cliente_id AND deleted_at IS NULL');
        return $stmt->execute([
            'id' => $id,
            'cliente_id' => $clienteId,
            'tipo' => $data['tipo'],
            'brand' => $data['brand'] !== '' ? $data['brand'] : null,
            'ultimo_cuatro' => $data['ultimo_cuatro'] !== '' ? $data['ultimo_cuatro'] : null,
            'tipo_tarjeta' => $data['tipo_tarjeta'] !== '' ? $data['tipo_tarjeta'] : null,
            'token' => $existing['token'] ?: self::buildToken($data),
            'payment_method_id' => $existing['payment_method_id'] ?: self::buildPaymentMethodId($data),
            'customer_id' => $existing['customer_id'] ?: self::buildCustomerId($clienteId),
            'nickname' => $data['nickname'] !== '' ? $data['nickname'] : null,
            'es_predeterminado' => !empty($data['es_predeterminado']) ? 1 : 0,
            'activo' => !empty($data['activo']) ? 1 : 0,
        ]);
    }

    public static function softDelete(int $id, int $clienteId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE metodos_pago SET deleted_at = NOW(), activo = 0, es_predeterminado = 0 WHERE id = :id AND cliente_id = :cliente_id AND deleted_at IS NULL');
        return $stmt->execute(['id' => $id, 'cliente_id' => $clienteId]);
    }

    private static function clearDefault(int $clienteId, ?int $exceptId = null): void
    {
        $db = Database::getInstance();
        if ($exceptId !== null) {
            $stmt = $db->prepare('UPDATE metodos_pago SET es_predeterminado = 0 WHERE cliente_id = :cliente_id AND id != :except_id AND deleted_at IS NULL');
            $stmt->execute(['cliente_id' => $clienteId, 'except_id' => $exceptId]);
            return;
        }

        $stmt = $db->prepare('UPDATE metodos_pago SET es_predeterminado = 0 WHERE cliente_id = :cliente_id AND deleted_at IS NULL');
        $stmt->execute(['cliente_id' => $clienteId]);
    }

    private static function buildToken(array $data): ?string
    {
        if (($data['tipo'] ?? '') !== 'tarjeta') {
            return null;
        }

        return 'tok_' . bin2hex(random_bytes(8));
    }

    private static function buildPaymentMethodId(array $data): string
    {
        return strtolower((string) ($data['tipo'] ?? 'metodo')) . '_' . bin2hex(random_bytes(4));
    }

    private static function buildCustomerId(int $clienteId): string
    {
        return 'cus_' . $clienteId . '_' . bin2hex(random_bytes(3));
    }
}
