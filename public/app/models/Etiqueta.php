<?php

class Etiqueta extends BaseAttributeModel
{
    protected static string $table = 'etiquetas';
    protected static array $fillable = ['nombre', 'slug', 'color'];

    /**
     * Crear etiqueta con color opcional
     */
    public static function createWithColor(string $nombre, string $color = '#3B82F6'): int
    {
        return parent::create([
            'nombre' => $nombre,
            'slug' => self::slugify($nombre),
            'color' => $color
        ]);
    }

    /**
     * Obtener etiquetas para un producto especÃ­fico
     */
    public static function forProduct(int $productId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT e.id, e.nombre, e.slug, e.color
             FROM etiquetas e
             INNER JOIN productos_etiquetas pe ON pe.etiqueta_id = e.id
             WHERE pe.producto_id = :productId
               AND e.deleted_at IS NULL
             ORDER BY e.nombre'
        );
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Contar usos en productos
     */
    public static function countUsages(int $id): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) as total FROM productos_etiquetas WHERE etiqueta_id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}
