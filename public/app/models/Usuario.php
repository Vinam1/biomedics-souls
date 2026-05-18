<?php

class Usuario
{
    public static function findByEmail(string $email): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE email = :email AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $nombre, string $apellidos, string $email, string $password): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO usuarios (nombre, apellidos, email, password_hash) VALUES (:nombre, :apellidos, :email, :password_hash)');
        $stmt->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $db->lastInsertId();
    }

    public static function updateProfile(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email, telefono = :telefono WHERE id = :id AND deleted_at IS NULL');
        return $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'telefono' => $data['telefono'] !== '' ? $data['telefono'] : null,
        ]);
    }

    public static function adminClientList(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono, u.created_at,
                    COALESCE(
                        (
                            SELECT p.direccion_ciudad
                            FROM pedidos p
                            WHERE p.cliente_id = u.id
                              AND p.deleted_at IS NULL
                            ORDER BY p.created_at DESC, p.id DESC
                            LIMIT 1
                        ),
                        (
                            SELECT d.ciudad
                            FROM direcciones d
                            WHERE d.cliente_id = u.id
                              AND d.deleted_at IS NULL
                            ORDER BY d.es_principal DESC, d.updated_at DESC, d.id DESC
                            LIMIT 1
                        )
                    ) AS municipio,
                    (
                        SELECT p.estado_pedido
                        FROM pedidos p
                        WHERE p.cliente_id = u.id
                          AND p.deleted_at IS NULL
                        ORDER BY p.created_at DESC, p.id DESC
                        LIMIT 1
                    ) AS ultimo_pedido_estatus,
                    (
                        SELECT COUNT(*)
                        FROM pedidos p
                        WHERE p.cliente_id = u.id
                          AND p.deleted_at IS NULL
                    ) AS total_pedidos
             FROM usuarios u
             WHERE u.role = "cliente"
               AND u.deleted_at IS NULL
             ORDER BY u.created_at DESC, u.id DESC'
        );

        return $stmt->fetchAll();
    }

    public static function findClientByIdForAdmin(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT u.*,
                    (
                        SELECT COUNT(*)
                        FROM pedidos p
                        WHERE p.cliente_id = u.id
                          AND p.deleted_at IS NULL
                    ) AS total_pedidos,
                    (
                        SELECT COALESCE(SUM(p.total), 0)
                        FROM pedidos p
                        WHERE p.cliente_id = u.id
                          AND p.deleted_at IS NULL
                          AND p.estado_pedido <> "cancelado"
                    ) AS total_gastado,
                    (
                        SELECT p.estado_pedido
                        FROM pedidos p
                        WHERE p.cliente_id = u.id
                          AND p.deleted_at IS NULL
                        ORDER BY p.created_at DESC, p.id DESC
                        LIMIT 1
                    ) AS ultimo_pedido_estatus,
                    (
                        SELECT p.created_at
                        FROM pedidos p
                        WHERE p.cliente_id = u.id
                          AND p.deleted_at IS NULL
                        ORDER BY p.created_at DESC, p.id DESC
                        LIMIT 1
                    ) AS ultimo_pedido_fecha
             FROM usuarios u
             WHERE u.id = :id
               AND u.role = "cliente"
               AND u.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
}
