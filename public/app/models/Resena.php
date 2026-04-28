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
            'SELECT r.id, r.calificacion, r.titulo, r.comentario, r.created_at,
                    p.nombre as producto_nombre, p.slug as producto_slug
             FROM resenas r
             INNER JOIN productos p ON p.id = r.producto_id
             WHERE r.cliente_id = :userId 
               AND r.estatus = "publicada" 
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
            'titulo'      => $data['titulo'] ?? null,
            'comentario'  => $data['comentario'] ?? null,
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
}