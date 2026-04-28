<?php

class Direccion
{
    public static function allByClienteId(int $clienteId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM direcciones WHERE cliente_id = :cliente_id AND deleted_at IS NULL ORDER BY es_principal DESC, updated_at DESC');
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function findByIdForCliente(int $id, int $clienteId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM direcciones WHERE id = :id AND cliente_id = :cliente_id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id, 'cliente_id' => $clienteId]);
        return $stmt->fetch() ?: null;
    }

    public static function defaultForCliente(int $clienteId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM direcciones WHERE cliente_id = :cliente_id AND deleted_at IS NULL ORDER BY es_principal DESC, updated_at DESC LIMIT 1');
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $clienteId, array $data): int
    {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            if (!empty($data['es_principal']) || !self::hasAnyActiveAddress($clienteId)) {
                self::clearPrincipal($clienteId);
                $data['es_principal'] = true;
            }

            $stmt = $db->prepare('INSERT INTO direcciones (cliente_id, calle, numero_exterior, numero_interior, colonia, ciudad, estado, pais, codigo_postal, referencias, es_principal) VALUES (:cliente_id, :calle, :numero_exterior, :numero_interior, :colonia, :ciudad, :estado, :pais, :codigo_postal, :referencias, :es_principal)');
            $stmt->execute([
                'cliente_id' => $clienteId,
                'calle' => $data['calle'],
                'numero_exterior' => $data['numero_exterior'],
                'numero_interior' => $data['numero_interior'] !== '' ? $data['numero_interior'] : null,
                'colonia' => $data['colonia'],
                'ciudad' => $data['ciudad'],
                'estado' => $data['estado'],
                'pais' => $data['pais'],
                'codigo_postal' => $data['codigo_postal'],
                'referencias' => $data['referencias'] !== '' ? $data['referencias'] : null,
                'es_principal' => !empty($data['es_principal']) ? 1 : 0,
            ]);

            $addressId = (int) $db->lastInsertId();
            $db->commit();

            return $addressId;
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }
    }

    public static function update(int $id, int $clienteId, array $data): bool
    {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            if (!empty($data['es_principal'])) {
                self::clearPrincipal($clienteId, $id);
            }

            $stmt = $db->prepare('UPDATE direcciones SET calle = :calle, numero_exterior = :numero_exterior, numero_interior = :numero_interior, colonia = :colonia, ciudad = :ciudad, estado = :estado, pais = :pais, codigo_postal = :codigo_postal, referencias = :referencias, es_principal = :es_principal WHERE id = :id AND cliente_id = :cliente_id AND deleted_at IS NULL');
            $success = $stmt->execute([
                'id' => $id,
                'cliente_id' => $clienteId,
                'calle' => $data['calle'],
                'numero_exterior' => $data['numero_exterior'],
                'numero_interior' => $data['numero_interior'] !== '' ? $data['numero_interior'] : null,
                'colonia' => $data['colonia'],
                'ciudad' => $data['ciudad'],
                'estado' => $data['estado'],
                'pais' => $data['pais'],
                'codigo_postal' => $data['codigo_postal'],
                'referencias' => $data['referencias'] !== '' ? $data['referencias'] : null,
                'es_principal' => !empty($data['es_principal']) ? 1 : 0,
            ]);

            if (!$data['es_principal']) {
                self::ensurePrincipalExists($clienteId);
            }

            $db->commit();
            return $success;
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }
    }

    public static function softDelete(int $id, int $clienteId): bool
    {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare('UPDATE direcciones SET deleted_at = NOW(), es_principal = 0 WHERE id = :id AND cliente_id = :cliente_id AND deleted_at IS NULL');
            $success = $stmt->execute(['id' => $id, 'cliente_id' => $clienteId]);
            self::ensurePrincipalExists($clienteId);
            $db->commit();

            return $success;
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }
    }

    private static function clearPrincipal(int $clienteId, ?int $exceptId = null): void
    {
        $db = Database::getInstance();
        if ($exceptId !== null) {
            $stmt = $db->prepare('UPDATE direcciones SET es_principal = 0 WHERE cliente_id = :cliente_id AND id != :except_id AND deleted_at IS NULL');
            $stmt->execute(['cliente_id' => $clienteId, 'except_id' => $exceptId]);
            return;
        }

        $stmt = $db->prepare('UPDATE direcciones SET es_principal = 0 WHERE cliente_id = :cliente_id AND deleted_at IS NULL');
        $stmt->execute(['cliente_id' => $clienteId]);
    }

    private static function hasAnyActiveAddress(int $clienteId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM direcciones WHERE cliente_id = :cliente_id AND deleted_at IS NULL');
        $stmt->execute(['cliente_id' => $clienteId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private static function ensurePrincipalExists(int $clienteId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id FROM direcciones WHERE cliente_id = :cliente_id AND deleted_at IS NULL ORDER BY es_principal DESC, updated_at DESC LIMIT 1');
        $stmt->execute(['cliente_id' => $clienteId]);
        $addressId = (int) $stmt->fetchColumn();

        if ($addressId <= 0) {
            return;
        }

        $promoteStmt = $db->prepare('UPDATE direcciones SET es_principal = CASE WHEN id = :id THEN 1 ELSE 0 END WHERE cliente_id = :cliente_id AND deleted_at IS NULL');
        $promoteStmt->execute([
            'id' => $addressId,
            'cliente_id' => $clienteId,
        ]);
    }
}
