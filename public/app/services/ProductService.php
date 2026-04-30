<?php

class ProductService
{
    public function validateFormData(array $data, array $files = [], ?int $existingId = null): array
    {
        $errors = [];

        if (empty(trim($data['nombre'] ?? ''))) {
            $errors[] = 'El nombre del producto es obligatorio.';
        }

        if (empty(trim($data['slug'] ?? ''))) {
            $errors[] = 'El slug del producto es obligatorio.';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $errors[] = 'El slug solo puede contener letras minúsculas, números y guiones.';
        }

        if (empty(trim($data['sku'] ?? ''))) {
            $errors[] = 'El SKU del producto es obligatorio.';
        }

        // Validación de duplicados
        if ($existingId === null) {
            if (Producto::findBySlug($data['slug'] ?? '')) {
                $errors[] = 'Ya existe un producto con ese slug.';
            }
            if (Producto::findBySku($data['sku'] ?? '')) {
                $errors[] = 'Ya existe un producto con ese SKU.';
            }
        } else {
            $existingBySlug = Producto::findBySlug($data['slug'] ?? '');
            if ($existingBySlug && (int)$existingBySlug['id'] !== $existingId) {
                $errors[] = 'Ya existe un producto con ese slug.';
            }
            $existingBySku = Producto::findBySku($data['sku'] ?? '');
            if ($existingBySku && (int)$existingBySku['id'] !== $existingId) {
                $errors[] = 'Ya existe un producto con ese SKU.';
            }
        }

        if (!isset($data['precio']) || !is_numeric($data['precio']) || (float)$data['precio'] <= 0) {
            $errors[] = 'El precio debe ser mayor a cero.';
        }

        if (!empty($data['precio_descuento']) && 
            (!is_numeric($data['precio_descuento']) || (float)$data['precio_descuento'] < 0)) {
            $errors[] = 'El precio de descuento debe ser un número positivo o estar vacío.';
        }

        if (!empty($data['precio_descuento']) && (float)$data['precio_descuento'] >= (float)($data['precio'] ?? 0)) {
            $errors[] = 'El precio de descuento debe ser menor al precio regular.';
        }

        if (empty($data['categoria_id'])) {
            $errors[] = 'La categoría es obligatoria.';
        }

        if (empty($data['forma_id'])) {
            $errors[] = 'La forma de presentación es obligatoria.';
        }

        // Validación básica de imágenes (solo si se suben)
        if (!empty($files['imagenes']['name'][0])) {
            foreach ($files['imagenes']['error'] as $err) {
                if ($err !== UPLOAD_ERR_OK && $err !== UPLOAD_ERR_NO_FILE) {
                    $errors[] = 'Error al procesar una de las imágenes.';
                }
            }
        }

        return $errors;
    }

    public function prepareProductData(array $postData): array
    {
        $contenidoNetoValor = trim($postData['contenido_neto_valor'] ?? '');
        $contenidoNetoUnidad = trim($postData['contenido_neto_unidad'] ?? 'mg');
        $contenidoNeto = $contenidoNetoValor !== '' 
            ? str_replace(',', '.', $contenidoNetoValor) . ' ' . $contenidoNetoUnidad 
            : null;

        return [
            'nombre'            => trim($postData['nombre'] ?? ''),
            'slug'              => strtolower(trim($postData['slug'] ?? '')),
            'sku'               => trim($postData['sku'] ?? ''),
            'precio'            => (float) ($postData['precio'] ?? 0),
            'precio_descuento'  => !empty($postData['precio_descuento']) ? (float)$postData['precio_descuento'] : null,
            'descripcion_corta' => trim($postData['descripcion_corta'] ?? ''),
            'descripcion_larga' => trim($postData['descripcion_larga'] ?? ''),
            'modo_empleo'       => trim($postData['modo_empleo'] ?? ''),
            'usos'              => trim($postData['usos'] ?? ''),
            'beneficios'        => trim($postData['beneficios'] ?? ''),
            'contenido_neto'    => $contenidoNeto,
            'cantidad_envase'   => trim($postData['cantidad_unidades'] ?? $postData['cantidad_envase'] ?? ''),
            'destacado'         => isset($postData['destacado']) ? 1 : 0,
            'categoria_id'      => $this->resolveCategoryId($postData),
            'forma_id'          => $this->resolveFormId($postData),
            'estatus'           => trim($postData['estatus'] ?? 'activo'),
            'existing_image_ids'=> array_values(array_filter(array_map('intval', $postData['existing_image_ids'] ?? []))),
            'image_order'       => array_values(array_filter(array_map('trim', $postData['image_order'] ?? []))),
        ];
    }

    private function resolveCategoryId(array $postData): int
    {
        $categoriaId = $postData['categoria_id'] ?? '';
        $newCategoryName = trim($postData['new_category_name'] ?? '');

        if (strpos($categoriaId, 'new-') === 0 && $newCategoryName !== '') {
            return Categoria::create($newCategoryName);
        }

        return (int) $categoriaId;
    }

    private function resolveFormId(array $postData): int
    {
        $formaId = $postData['forma_id'] ?? '';
        $newFormName = trim($postData['new_form_name'] ?? '');

        if (strpos($formaId, 'new-') === 0 && $newFormName !== '') {
            return Forma::create($newFormName);
        }

        return (int) $formaId;
    }

    public function prepareRelationIds(array $postData): array
    {
        return [
            'etiquetas' => $this->resolveRelationIds($postData['etiquetas'] ?? [], 'etiqueta'),
        ];
    }

    private function resolveRelationIds(array $items, string $type): array
    {
        $ids = [];
        foreach ($items as $item) {
            if (is_numeric($item) && $item > 0) {
                $ids[] = (int) $item;
                continue;
            }
            if (strpos($item, 'new:') === 0) {
                $name = trim(substr($item, 4));
                if ($name !== '' && $type === 'etiqueta') {
                    $ids[] = Etiqueta::createWithColor($name);
                }
            }
        }
        return array_values(array_unique($ids));
    }

    public function buildRelationPayload(array $items, array $available): array
    {
        $payload = [];
        foreach ($items as $item) {
            if (is_numeric($item)) {
                $id = (int)$item;
                foreach ($available as $entry) {
                    if (isset($entry['id']) && (int)$entry['id'] === $id) {
                        $payload[] = ['id' => $id, 'nombre' => $entry['nombre']];
                        break;
                    }
                }
            }
        }
        return $payload;
    }

    public function getProductWithRelations(int $id): ?array
    {
        $product = Producto::findById($id);
        if (!$product) return null;

        $product['etiquetas'] = Etiqueta::forProduct($id);
        $product['images']    = Producto::images($id);

        return $product;
    }

    /**
     * MÉTODO CORREGIDO Y SIMPLIFICADO
     */
    public function saveProduct(array $productData, array $relationIds, array $files = [], ?int $existingId = null): int
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            if ($existingId !== null) {
                $updated = Producto::update($existingId, $productData);
                if (!$updated) {
                    throw new RuntimeException('No se pudo actualizar el producto.');
                }
                $productId = $existingId;
            } else {
                $productId = Producto::create($productData);
                if (!$productId || $productId <= 0) {
                    throw new RuntimeException('No se pudo crear el producto en la base de datos.');
                }
            }

            // Sincronizar etiquetas
            if (!empty($relationIds['etiquetas'])) {
                Producto::syncEtiquetas($productId, $relationIds['etiquetas']);
            }

            // Temporalmente desactivamos imágenes para depurar
            // Producto::syncImages($productId, $productData, $files['imagenes'] ?? []);

            $db->commit();
            return $productId;

        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log("ERROR AL GUARDAR PRODUCTO: " . $e->getMessage());
            error_log("Archivo: " . $e->getFile() . " | Línea: " . $e->getLine());
            error_log("Datos: " . json_encode($productData, JSON_UNESCAPED_UNICODE));

            throw new RuntimeException("Error al guardar el producto: " . $e->getMessage());
        }
    }

    private function normalizeStatus(?string $status): string
    {
        return trim((string) $status) ?: 'activo';
    }
}