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
}
