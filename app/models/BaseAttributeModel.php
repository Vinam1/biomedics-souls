<?php

abstract class BaseAttributeModel
{
    protected static string $table;
    protected static array $fillable = ['nombre', 'slug'];
    protected static array $searchable = ['nombre'];

    /**
     * Obtener todos los registros activos
     */
    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM ' . static::$table . ' WHERE deleted_at IS NULL ORDER BY nombre');
        return $stmt->fetchAll();
    }

    /**
     * Buscar por ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM ' . static::$table . ' WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Buscar por nombre
     */
    public static function findByName(string $nombre): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM ' . static::$table . ' WHERE nombre = :nombre AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['nombre' => $nombre]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Búsqueda con paginación
     */
    public static function search(string $query = '', int $limit = 20, int $offset = 0): array
    {
        $db = Database::getInstance();

        if (empty($query)) {
            $stmt = $db->prepare('SELECT * FROM ' . static::$table . ' WHERE deleted_at IS NULL ORDER BY nombre LIMIT :limit OFFSET :offset');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        } else {
            $searchTerm = '%' . $query . '%';
            $stmt = $db->prepare('SELECT * FROM ' . static::$table . ' WHERE deleted_at IS NULL AND nombre LIKE :query ORDER BY nombre LIMIT :limit OFFSET :offset');
            $stmt->bindValue(':query', $searchTerm);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Contar total de registros
     */
    public static function count(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT COUNT(*) as total FROM ' . static::$table . ' WHERE deleted_at IS NULL');
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    /**
     * Contar usos en productos
     */
    abstract public static function countUsages(int $id): int;

    /**
     * Crear nuevo registro
     */
    public static function create(array $data): int
    {
        $existing = static::findByName($data['nombre']);
        if ($existing) {
            return (int) $existing['id'];
        }

        $data['slug'] = $data['slug'] ?? static::slugify($data['nombre']);

        $db = Database::getInstance();
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $db->prepare($sql);
        $stmt->execute($data);

        return (int) $db->lastInsertId();
    }

    /**
     * Actualizar registro
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if (in_array($key, static::$fillable)) {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE ' . static::$table . ' SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Soft delete
     */
    public static function softDelete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE ' . static::$table . ' SET deleted_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Generar slug
     */
    protected static function slugify(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/[\s-]+/', '-', $value);
        return trim($value, '-');
    }
}