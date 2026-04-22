<?php

class AttributeService
{
    private const TYPES = ['etiquetas'];
    private const MODEL_MAP = [
        'etiquetas' => 'Etiqueta'
    ];

    private const TITLES = [
        'etiquetas' => 'Etiquetas'
    ];

    private const DESCRIPTIONS = [
        'etiquetas' => 'Gestiona las etiquetas que clasifican tus productos'
    ];

    /**
     * Valida si un tipo de atributo es válido
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES);
    }

    /**
     * Obtiene la clase del modelo para un tipo
     */
    public static function getModelClass(string $type): ?string
    {
        return self::MODEL_MAP[$type] ?? null;
    }

    /**
     * Obtiene el título para un tipo
     */
    public static function getTitle(string $type): string
    {
        return self::TITLES[$type] ?? 'Atributos';
    }

    /**
     * Obtiene la descripción para un tipo
     */
    public static function getDescription(string $type): string
    {
        return self::DESCRIPTIONS[$type] ?? '';
    }

    /**
     * Obtiene datos paginados con estadísticas
     */
    public static function getPaginatedData(string $type, int $page, string $search, int $perPage = 15): array
    {
        $modelClass = self::getModelClass($type);
        if (!$modelClass) {
            return [];
        }

        $offset = ($page - 1) * $perPage;

        // Obtener items paginados
        $items = $modelClass::search($search, $perPage, $offset);

        // Contar total de manera eficiente
        $total = $modelClass::count();
        if (!empty($search)) {
            // Si hay búsqueda, contar solo los resultados de búsqueda
            $allSearchResults = $modelClass::search($search, 9999, 0);
            $total = count($allSearchResults);
        }

        $totalPages = ceil($total / $perPage);

        // Agregar estadísticas de uso
        $itemsWithStats = array_map(function($item) use ($modelClass) {
            $item['usages'] = $modelClass::countUsages((int)$item['id']);
            return $item;
        }, $items);

        return [
            'items' => $itemsWithStats,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search
        ];
    }

    /**
     * Valida datos del formulario
     */
    public static function validateFormData(string $type, array $data): array
    {
        $errors = [];

        $nombre = trim($data['nombre'] ?? '');
        $slug = trim($data['slug'] ?? '');

        if (empty($nombre)) {
            $errors[] = 'El nombre es requerido';
        }

        if (empty($slug)) {
            $errors[] = 'El slug es requerido';
        }

        return $errors;
    }

    /**
     * Prepara datos para crear/actualizar
     */
    public static function prepareData(string $type, array $data): array
    {
        $nombre = trim($data['nombre'] ?? '');
        $slug = trim($data['slug'] ?? '');

        $prepared = [
            'nombre' => $nombre,
            'slug' => !empty($slug) ? $slug : self::generateSlug($nombre),
        ];

        // Campos específicos por tipo
        if ($type === 'etiquetas') {
            $prepared['color'] = $data['color'] ?? '#3B82F6';
        }

        return $prepared;
    }

    /**
     * Verifica si un atributo puede ser eliminado
     */
    public static function canDelete(string $type, int $id): array
    {
        $modelClass = self::getModelClass($type);
        if (!$modelClass) {
            return ['canDelete' => false, 'message' => 'Tipo de atributo inválido'];
        }

        $usages = $modelClass::countUsages($id);
        if ($usages > 0) {
            return [
                'canDelete' => false,
                'message' => "No se puede eliminar porque está siendo usado por {$usages} producto(s). Primero remuévelo de esos productos."
            ];
        }

        return ['canDelete' => true, 'message' => ''];
    }

    /**
     * Genera slug automáticamente
     */
    public static function generateSlug(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/[\s-]+/', '-', $value);
        return trim($value, '-');
    }
}