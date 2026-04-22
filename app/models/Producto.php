<?php

class Producto
{
    public static function featured(int $limit = 8): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.estatus,
                    c.nombre AS categoria_nombre,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.deleted_at IS NULL AND p.estatus = "activo"
             ORDER BY p.destacado DESC, p.updated_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.descripcion_larga,
                    p.modo_empleo, p.usos, p.beneficios, p.contenido_neto, p.estatus, p.calificacion_promedio, p.total_resenas,
                    c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.slug = :slug AND p.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public static function allActive(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.estatus,
                    c.nombre AS categoria_nombre,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.deleted_at IS NULL AND p.estatus = "activo"
             ORDER BY p.updated_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function findByCategorySlug(string $slug): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.estatus,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE c.slug = :slug AND p.deleted_at IS NULL AND p.estatus = "activo"
             ORDER BY p.updated_at DESC'
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchAll();
    }

    public static function images(int $productId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, url_imagen, alt_text, es_principal, orden
             FROM productos_imagenes
             WHERE producto_id = :productId AND deleted_at IS NULL
             ORDER BY es_principal DESC, orden ASC'
        );
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.estatus,
                    c.nombre AS categoria_nombre,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.deleted_at IS NULL
             ORDER BY p.updated_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function search(array $filters = []): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.estatus, p.updated_at,
                       c.nombre AS categoria_nombre,
                       (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.deleted_at IS NULL';
        $params = [];

        if (!empty($filters['query'])) {
            $sql .= ' AND (p.nombre LIKE :query OR p.slug LIKE :query OR p.sku LIKE :query)';
            $params['query'] = '%' . $filters['query'] . '%';
        }

        if (!empty($filters['categoria_id'])) {
            $sql .= ' AND p.categoria_id = :categoria_id';
            $params['categoria_id'] = $filters['categoria_id'];
        }

        if (!empty($filters['estatus'])) {
            $sql .= ' AND p.estatus = :estatus';
            $params['estatus'] = $filters['estatus'];
        }

        $sort = $filters['sort'] ?? 'updated_at_desc';
        switch ($sort) {
            case 'updated_at_asc':
                $sql .= ' ORDER BY p.updated_at ASC';
                break;
            case 'precio_asc':
                $sql .= ' ORDER BY p.precio ASC';
                break;
            case 'precio_desc':
                $sql .= ' ORDER BY p.precio DESC';
                break;
            case 'nombre_asc':
                $sql .= ' ORDER BY p.nombre ASC';
                break;
            case 'nombre_desc':
                $sql .= ' ORDER BY p.nombre DESC';
                break;
            default:
                $sql .= ' ORDER BY p.updated_at DESC';
                break;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT COUNT(*) AS total FROM productos WHERE deleted_at IS NULL');
        $row = $stmt->fetch();
        return $row ? (int) $row['total'] : 0;
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.descripcion_larga,
                    p.modo_empleo, p.usos, p.beneficios, p.contenido_neto, p.cantidad_envase, p.destacado, p.estatus, p.categoria_id, p.forma_id,
                    c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.id = :id AND p.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBySku(string $sku): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, sku, nombre
             FROM productos
             WHERE sku = :sku AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['sku' => $sku]);
        return $stmt->fetch() ?: null;
    }

    public static function findManyByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0)));
        if (empty($ids)) {
            return [];
        }

        $db = Database::getInstance();
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare(
            "SELECT p.id, p.nombre, p.slug, p.sku, p.precio, p.precio_descuento, p.descripcion_corta, p.descripcion_larga,
                    p.modo_empleo, p.usos, p.beneficios, p.contenido_neto, p.cantidad_envase, p.destacado, p.estatus, p.categoria_id, p.forma_id,
                    c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    (SELECT url_imagen FROM productos_imagenes WHERE producto_id = p.id AND es_principal = 1 AND deleted_at IS NULL LIMIT 1) AS imagen_principal
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.deleted_at IS NULL AND p.id IN ($placeholders)"
        );
        $stmt->execute($ids);

        $products = [];
        foreach ($stmt->fetchAll() as $product) {
            $products[(int) $product['id']] = $product;
        }

        return $products;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO productos (categoria_id, forma_id, nombre, slug, sku, precio, precio_descuento, descripcion_corta, descripcion_larga, modo_empleo, usos, beneficios, contenido_neto, cantidad_envase, destacado, estatus)
             VALUES (:categoria_id, :forma_id, :nombre, :slug, :sku, :precio, :precio_descuento, :descripcion_corta, :descripcion_larga, :modo_empleo, :usos, :beneficios, :contenido_neto, :cantidad_envase, :destacado, :estatus)'
        );
        $stmt->execute([
            'categoria_id' => $data['categoria_id'],
            'forma_id' => $data['forma_id'],
            'nombre' => $data['nombre'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'precio' => $data['precio'],
            'precio_descuento' => $data['precio_descuento'],
            'descripcion_corta' => $data['descripcion_corta'],
            'descripcion_larga' => $data['descripcion_larga'],
            'modo_empleo' => $data['modo_empleo'] ?? null,
            'usos' => $data['usos'] ?? null,
            'beneficios' => $data['beneficios'] ?? null,
            'contenido_neto' => $data['contenido_neto'],
            'cantidad_envase' => $data['cantidad_envase'],
            'destacado' => $data['destacado'],
            'estatus' => $data['estatus'],
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'UPDATE productos
             SET categoria_id = :categoria_id,
                 forma_id = :forma_id,
                 nombre = :nombre,
                 slug = :slug,
                 sku = :sku,
                 precio = :precio,
                 precio_descuento = :precio_descuento,
                 descripcion_corta = :descripcion_corta,
                 descripcion_larga = :descripcion_larga,
                 modo_empleo = :modo_empleo,
                 usos = :usos,
                 beneficios = :beneficios,
                 contenido_neto = :contenido_neto,
                 cantidad_envase = :cantidad_envase,
                 destacado = :destacado,
                 estatus = :estatus
             WHERE id = :id'
        );
        return $stmt->execute([
            'categoria_id' => $data['categoria_id'],
            'forma_id' => $data['forma_id'],
            'nombre' => $data['nombre'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'precio' => $data['precio'],
            'precio_descuento' => $data['precio_descuento'],
            'descripcion_corta' => $data['descripcion_corta'],
            'descripcion_larga' => $data['descripcion_larga'],
            'modo_empleo' => $data['modo_empleo'] ?? null,
            'usos' => $data['usos'] ?? null,
            'beneficios' => $data['beneficios'] ?? null,
            'contenido_neto' => $data['contenido_neto'],
            'cantidad_envase' => $data['cantidad_envase'],
            'destacado' => $data['destacado'],
            'estatus' => $data['estatus'],
            'id' => $id,
        ]);
    }

    public static function syncEtiquetas(int $productId, array $etiquetaIds): void
    {
        $db = Database::getInstance();
        $db->prepare('DELETE FROM productos_etiquetas WHERE producto_id = :productId')->execute(['productId' => $productId]);

        $stmt = $db->prepare('INSERT INTO productos_etiquetas (producto_id, etiqueta_id) VALUES (:productId, :etiquetaId)');
        foreach (array_unique($etiquetaIds) as $etiquetaId) {
            if ($etiquetaId <= 0) {
                continue;
            }
            $stmt->execute(['productId' => $productId, 'etiquetaId' => $etiquetaId]);
        }
    }

    public static function syncImages(int $productId, array $productData, array $files): void
    {
        $db = Database::getInstance();
        $existingImageIds = array_values(array_filter(array_map('intval', $productData['existing_image_ids'] ?? [])));
        $imageOrder = array_values(array_filter(array_map('trim', $productData['image_order'] ?? [])));
        $currentImages = self::images($productId);
        $currentById = [];

        foreach ($currentImages as $image) {
            $currentById[(int) $image['id']] = $image;
        }

        $currentIds = array_keys($currentById);
        $idsToDelete = array_diff($currentIds, $existingImageIds);
        if (!empty($idsToDelete)) {
            self::softDeleteImages($productId, $idsToDelete, $currentById);
        }

        $uploadedImages = self::storeUploadedImages($productId, $files);
        $keptExisting = array_values(array_filter($currentImages, static function ($image) use ($existingImageIds) {
            return in_array((int) $image['id'], $existingImageIds, true);
        }));

        $orderedEntries = [];
        $existingById = [];
        foreach ($keptExisting as $image) {
            $existingById[(int) $image['id']] = $image;
        }

        $newIndex = 0;
        foreach ($imageOrder as $entry) {
            if (str_starts_with($entry, 'existing:')) {
                $imageId = (int) substr($entry, 9);
                if (isset($existingById[$imageId])) {
                    $orderedEntries[] = [
                        'kind' => 'existing',
                        'id' => $imageId,
                    ];
                }
                continue;
            }

            if ($entry === 'new' && isset($uploadedImages[$newIndex])) {
                $orderedEntries[] = [
                    'kind' => 'new',
                    'filename' => $uploadedImages[$newIndex],
                ];
                $newIndex++;
            }
        }

        while ($newIndex < count($uploadedImages)) {
            $orderedEntries[] = [
                'kind' => 'new',
                'filename' => $uploadedImages[$newIndex],
            ];
            $newIndex++;
        }

        if (empty($orderedEntries)) {
            foreach ($keptExisting as $image) {
                $orderedEntries[] = [
                    'kind' => 'existing',
                    'id' => (int) $image['id'],
                ];
            }
        }

        self::applyImageOrdering($productId, $orderedEntries, $existingById);
    }

    private static function storeUploadedImages(int $productId, array $files): array
    {
        if (!isset($files['name']) || !is_array($files['name']) || empty($files['name'])) {
            return [];
        }

        $storageDir = dirname(__DIR__, 2) . '/public/assets/img/products';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeToExtension = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $savedPaths = [];
        $savedFiles = [];

        foreach ($files['name'] as $index => $filename) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (!isset($files['tmp_name'][$index]) || $files['error'][$index] !== UPLOAD_ERR_OK) {
                self::deleteStoredFiles($savedPaths);
                throw new RuntimeException('No se pudo procesar una de las imágenes seleccionadas.');
            }

            $tmpName = $files['tmp_name'][$index];
            $size = (int) ($files['size'][$index] ?? 0);
            if ($size <= 0 || $size > MAX_UPLOAD_SIZE) {
                self::deleteStoredFiles($savedPaths);
                throw new RuntimeException('Cada imagen debe pesar máximo 5 MB.');
            }

            $mimeType = $finfo->file($tmpName);
            if (!isset($mimeToExtension[$mimeType]) || @getimagesize($tmpName) === false) {
                self::deleteStoredFiles($savedPaths);
                throw new RuntimeException('Solo se permiten imágenes JPG, PNG, WEBP o GIF válidas.');
            }

            $extension = $mimeToExtension[$mimeType];
            $uniqueName = sprintf('%d_%s.%s', $productId, bin2hex(random_bytes(16)), $extension);
            $destination = $storageDir . '/' . $uniqueName;

            if (!move_uploaded_file($tmpName, $destination)) {
                self::deleteStoredFiles($savedPaths);
                throw new RuntimeException('No se pudo guardar una de las imágenes del producto.');
            }

            $savedPaths[] = $destination;
            $savedFiles[] = $uniqueName;
        }

        return $savedFiles;
    }

    private static function applyImageOrdering(int $productId, array $orderedEntries, array $existingById): void
    {
        $db = Database::getInstance();
        $insertStmt = $db->prepare(
            'INSERT INTO productos_imagenes (producto_id, url_imagen, alt_text, es_principal, orden)
             VALUES (:productId, :urlImagen, :altText, :esPrincipal, :orden)'
        );
        $updateStmt = $db->prepare(
            'UPDATE productos_imagenes
             SET es_principal = :esPrincipal, orden = :orden
             WHERE id = :id AND producto_id = :productId AND deleted_at IS NULL'
        );

        foreach ($orderedEntries as $index => $entry) {
            $isPrimary = $index === 0 ? 1 : 0;

            if ($entry['kind'] === 'existing') {
                $imageId = (int) $entry['id'];
                if (!isset($existingById[$imageId])) {
                    continue;
                }

                $updateStmt->execute([
                    'esPrincipal' => $isPrimary,
                    'orden' => $index,
                    'id' => $imageId,
                    'productId' => $productId,
                ]);
                continue;
            }

            $insertStmt->execute([
                'productId' => $productId,
                'urlImagen' => $entry['filename'],
                'altText' => null,
                'esPrincipal' => $isPrimary,
                'orden' => $index,
            ]);
        }
    }

    private static function softDeleteImages(int $productId, array $imageIds, array $currentById): void
    {
        if (empty($imageIds)) {
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE productos_imagenes SET deleted_at = NOW(), es_principal = 0 WHERE id = :id AND producto_id = :productId AND deleted_at IS NULL');
        $storageDir = dirname(__DIR__, 2) . '/public/assets/img/products/';

        foreach ($imageIds as $imageId) {
            $stmt->execute([
                'id' => $imageId,
                'productId' => $productId,
            ]);

            $filename = $currentById[(int) $imageId]['url_imagen'] ?? null;
            if ($filename) {
                $path = $storageDir . $filename;
                if (is_file($path)) {
                    @unlink($path);
                }
            }
        }
    }

    private static function deleteStoredFiles(array $savedPaths): void
    {
        foreach ($savedPaths as $savedPath) {
            if (is_file($savedPath)) {
                @unlink($savedPath);
            }
        }
    }

    public static function softDelete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE productos SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL');
        return $stmt->execute(['id' => $id]);
    }
}
