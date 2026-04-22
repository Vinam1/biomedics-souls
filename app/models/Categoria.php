<?php

class Categoria extends BaseEntityModel
{
    protected static string $table = 'categorias';
    protected static array $columns = ['id', 'nombre', 'slug'];
    protected static bool $hasSlug = true;

    public static function findBySlug(string $slug): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, nombre, slug FROM categorias WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public static function search(string $query = ''): array
    {
        $db = Database::getInstance();

        if ($query === '') {
            $stmt = $db->prepare("SELECT id, nombre, slug,
                                  (SELECT COUNT(*) FROM productos WHERE categoria_id = c.id AND deleted_at IS NULL) as product_count
                                  FROM categorias c
                                  WHERE deleted_at IS NULL
                                  ORDER BY nombre");
            $stmt->execute();
        } else {
            $searchTerm = '%' . $query . '%';
            $stmt = $db->prepare("SELECT id, nombre, slug,
                                  (SELECT COUNT(*) FROM productos WHERE categoria_id = c.id AND deleted_at IS NULL) as product_count
                                  FROM categorias c
                                  WHERE deleted_at IS NULL AND nombre LIKE :query
                                  ORDER BY nombre");
            $stmt->execute(['query' => $searchTerm]);
        }

        return $stmt->fetchAll();
    }

    public static function countProducts(int $id): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM productos WHERE categoria_id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? (int) $row['total'] : 0;
    }

    public static function create(string $nombre, ?string $slug = null): int
    {
        $slug = $slug !== null && $slug !== '' ? $slug : self::slugify($nombre);

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO categorias (nombre, slug, created_at) VALUES (:nombre, :slug, NOW())");
        $stmt->execute([
            'nombre' => $nombre,
            'slug' => $slug,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, string $nombre, ?string $slug = null): bool
    {
        $slug = $slug !== null && $slug !== '' ? $slug : self::slugify($nombre);

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE categorias SET nombre = :nombre, slug = :slug, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute([
            'nombre' => $nombre,
            'slug' => $slug,
            'id' => $id,
        ]);
    }

    public static function softDelete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE categorias SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, nombre, slug FROM categorias WHERE deleted_at IS NULL ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, nombre, slug FROM categorias WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    protected static function slugify(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/[\s-]+/', '-', $value);
        return trim($value, '-');
    }
}
