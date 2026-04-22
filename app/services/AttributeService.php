<?php

class AttributeService
{
    private const TYPES = ['etiquetas'];
    private const MODEL_MAP = [
        'etiquetas' => 'Etiqueta',
    ];

    private const TITLES = [
        'etiquetas' => 'Etiquetas',
    ];

    private const DESCRIPTIONS = [
        'etiquetas' => 'Gestiona las etiquetas que clasifican tus productos',
    ];

    public static function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }

    public static function getModelClass(string $type): ?string
    {
        return self::MODEL_MAP[$type] ?? null;
    }

    public static function getTitle(string $type): string
    {
        return self::TITLES[$type] ?? 'Atributos';
    }

    public static function getDescription(string $type): string
    {
        return self::DESCRIPTIONS[$type] ?? '';
    }

    public static function getPaginatedData(string $type, int $page, string $search, int $perPage = 15): array
    {
        $modelClass = self::getModelClass($type);
        if (!$modelClass) {
            return [];
        }

        $offset = ($page - 1) * $perPage;
        $items = $modelClass::search($search, $perPage, $offset);
        $total = $modelClass::countSearch($search);
        $totalPages = (int) ceil($total / $perPage);

        $itemsWithStats = array_map(static function (array $item) use ($modelClass): array {
            $item['usages'] = $modelClass::countUsages((int) $item['id']);
            return $item;
        }, $items);

        return [
            'items' => $itemsWithStats,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
        ];
    }

    public static function validateFormData(string $type, array $data): array
    {
        $errors = [];

        $nombre = trim($data['nombre'] ?? '');
        $slug = trim($data['slug'] ?? '');

        if ($nombre === '') {
            $errors[] = 'El nombre es requerido';
        }

        if ($slug === '') {
            $errors[] = 'El slug es requerido';
        }

        return $errors;
    }

    public static function prepareData(string $type, array $data): array
    {
        $nombre = trim($data['nombre'] ?? '');
        $slug = trim($data['slug'] ?? '');

        $prepared = [
            'nombre' => $nombre,
            'slug' => $slug !== '' ? $slug : self::generateSlug($nombre),
        ];

        if ($type === 'etiquetas') {
            $prepared['color'] = $data['color'] ?? '#3B82F6';
        }

        return $prepared;
    }

    public static function canDelete(string $type, int $id): array
    {
        $modelClass = self::getModelClass($type);
        if (!$modelClass) {
            return ['canDelete' => false, 'message' => 'Tipo de atributo invalido'];
        }

        $usages = $modelClass::countUsages($id);
        if ($usages > 0) {
            return [
                'canDelete' => false,
                'message' => "No se puede eliminar porque esta siendo usado por {$usages} producto(s). Primero remuevelo de esos productos.",
            ];
        }

        return ['canDelete' => true, 'message' => ''];
    }

    public static function generateSlug(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/[\s-]+/', '-', $value);
        return trim($value, '-');
    }
}
