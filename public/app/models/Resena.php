<?php

class Resena
{
    protected static string $table = 'resenas';
    protected static array $columns = ['id', 'cliente_id', 'producto_id', 'calificacion', 'titulo', 'comentario', 'estatus', 'created_at'];

    /**
     * Buscar reseÃ±a por ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM resenas WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Todas las reseÃ±as (para admin)
     */
    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM resenas WHERE deleted_at IS NULL ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function allForAdmin(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT r.id, r.calificacion, r.titulo, r.comentario, r.estatus, r.created_at,
                    u.id AS cliente_id, u.nombre, u.apellidos, u.email,
                    p.id AS producto_id, p.nombre AS producto_nombre, p.slug AS producto_slug
             FROM resenas r
             INNER JOIN usuarios u ON u.id = r.cliente_id
             INNER JOIN productos p ON p.id = r.producto_id
             WHERE r.deleted_at IS NULL
             ORDER BY r.created_at DESC, r.id DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * ReseÃ±as de un producto especÃ­fico (para pÃ¡gina de producto)
     */
    public static function forProduct(int $productId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT r.id, r.calificacion, r.titulo, r.comentario, r.created_at,
                    u.nombre, u.apellidos
             FROM resenas r
             INNER JOIN usuarios u ON u.id = r.cliente_id
             WHERE r.producto_id = :producto_id 
               AND r.estatus = "publicada" 
               AND r.deleted_at IS NULL
             ORDER BY r.created_at DESC'
        );
        $stmt->execute(['producto_id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * ReseÃ±as hechas por un usuario especÃ­fico (para "Mi Cuenta")
     */
    public static function findByUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT r.id, r.calificacion, r.titulo, r.comentario, r.estatus, r.created_at,
                    p.nombre as producto_nombre, p.slug as producto_slug
             FROM resenas r
             INNER JOIN productos p ON p.id = r.producto_id
             WHERE r.cliente_id = :userId 
               AND r.deleted_at IS NULL
             ORDER BY r.created_at DESC'
        );
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nueva reseÃ±a
     */
    public static function create(array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO resenas (cliente_id, producto_id, calificacion, titulo, comentario, estatus)
             VALUES (:cliente_id, :producto_id, :calificacion, :titulo, :comentario, "publicada")'
        );
        return $stmt->execute([
            'cliente_id'  => $data['cliente_id'],
            'producto_id' => $data['producto_id'],
            'calificacion'=> $data['calificacion'],
            'titulo'      => trim((string) ($data['titulo'] ?? '')) ?: null,
            'comentario'  => trim((string) ($data['comentario'] ?? '')) ?: null,
        ]);
    }

    /**
     * Verificar si el usuario ya reseÃ±Ã³ un producto
     */
    public static function userHasReviewed(int $userId, int $productId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM resenas 
             WHERE cliente_id = :cliente_id 
               AND producto_id = :producto_id 
               AND deleted_at IS NULL'
        );
        $stmt->execute([
            'cliente_id' => $userId,
            'producto_id' => $productId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si el usuario puede reseÃ±ar (solo si ya recibiÃ³ el producto)
     */
    public static function canReview(int $userId, int $productId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM pedidos_detalle pd
             INNER JOIN pedidos p ON p.id = pd.pedido_id
             WHERE pd.producto_id = :producto_id
               AND p.cliente_id = :cliente_id
               AND p.estado_pedido = "entregado"
               AND p.deleted_at IS NULL'
        );
        $stmt->execute([
            'cliente_id' => $userId,
            'producto_id' => $productId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public static function reviewableProductsForUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.calificacion_promedio,
                    MAX(pe.created_at) AS ultima_compra_fecha
             FROM pedidos pe
             INNER JOIN pedidos_detalle pd ON pd.pedido_id = pe.id
             INNER JOIN productos p ON p.id = pd.producto_id
             LEFT JOIN resenas r
                ON r.cliente_id = pe.cliente_id
               AND r.producto_id = p.id
               AND r.deleted_at IS NULL
             WHERE pe.cliente_id = :userId
               AND pe.estado_pedido = "entregado"
               AND pe.deleted_at IS NULL
               AND p.deleted_at IS NULL
               AND r.id IS NULL
             GROUP BY p.id, p.nombre, p.slug, p.sku, p.calificacion_promedio
             ORDER BY ultima_compra_fecha DESC, p.nombre ASC'
        );
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public static function findByClientForAdmin(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT r.id, r.calificacion, r.titulo, r.comentario, r.estatus, r.created_at,
                    p.nombre AS producto_nombre, p.slug AS producto_slug
             FROM resenas r
             INNER JOIN productos p ON p.id = r.producto_id
             WHERE r.cliente_id = :userId
               AND r.deleted_at IS NULL
             ORDER BY r.created_at DESC, r.id DESC'
        );
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $allowed = ['publicada', 'eliminada'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare(
            'UPDATE resenas
             SET estatus = :estatus
             WHERE id = :id
               AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'id' => $id,
            'estatus' => $status,
        ]);
    }
}
