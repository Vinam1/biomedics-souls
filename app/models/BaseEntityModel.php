<?php

abstract class BaseEntityModel
{
    protected static string $table;
    protected static array $columns = ['id', 'nombre'];
    protected static bool $hasSlug = false;

    public static function all(): array
    {
        $db = Database::getInstance();
        $columns = implode(', ', static::$columns);
        $orderBy = static::$hasSlug ? 'nombre' : 'nombre';
        $stmt = $db->query("SELECT {$columns} FROM " . static::$table . " WHERE deleted_at IS NULL ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public static function findByName(string $nombre): ?array
    {
        $db = Database::getInstance();
        $columns = implode(', ', static::$columns);
        $stmt = $db->prepare("SELECT {$columns} FROM " . static::$table . " WHERE nombre = :nombre AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['nombre' => $nombre]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $columns = implode(', ', static::$columns);
        $stmt = $db->prepare("SELECT {$columns} FROM " . static::$table . " WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $nombre): int
    {
        $existing = self::findByName($nombre);
        if ($existing) {
            return (int) $existing['id'];
        }

        $db = Database::getInstance();
        $data = ['nombre' => $nombre];
        $placeholders = [':nombre'];

        if (static::$hasSlug) {
            $data['slug'] = self::slugify($nombre);
            $placeholders[] = ':slug';
        }

        $columns = implode(', ', array_keys($data));
        $values = implode(', ', $placeholders);

        $stmt = $db->prepare("INSERT INTO " . static::$table . " ({$columns}) VALUES ({$values})");
        $stmt->execute($data);

        return (int) $db->lastInsertId();
    }

    protected static function slugify(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/[\s-]+/', '-', $value);
        return trim($value, '-');
    }

    public static function softDelete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE " . static::$table . " SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function count(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COUNT(*) FROM " . static::$table . " WHERE deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }
}