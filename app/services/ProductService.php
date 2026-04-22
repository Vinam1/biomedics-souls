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

        $existingBySlug = !empty($data['slug']) ? Producto::findBySlug($data['slug']) : null;
        if ($existingBySlug && (int) $existingBySlug['id'] !== $existingId) {
            $errors[] = 'Ya existe un producto con ese slug.';
        }

        $existingBySku = !empty($data['sku']) ? Producto::findBySku($data['sku']) : null;
        if ($existingBySku && (int) $existingBySku['id'] !== $existingId) {
            $errors[] = 'Ya existe un producto con ese SKU.';
        }

        if (!isset($data['precio']) || !is_numeric($data['precio']) || (float) $data['precio'] < 0) {
            $errors[] = 'El precio debe ser un número positivo.';
        }

        if (isset($data['precio_descuento']) && $data['precio_descuento'] !== '' && (!is_numeric($data['precio_descuento']) || (float) $data['precio_descuento'] < 0)) {
            $errors[] = 'El precio de descuento debe ser un número positivo o estar vacío.';
        }

        if (isset($data['precio_descuento']) && $data['precio_descuento'] !== '' && (float) $data['precio_descuento'] >= (float) $data['precio']) {
            $errors[] = 'El precio de descuento debe ser menor al precio regular.';
        }

        if (empty($data['categoria_id'])) {
            $errors[] = 'La categoría es obligatoria.';
        }

        if (empty($data['forma_id'])) {
            $errors[] = 'La forma es obligatoria.';
        }

        if (!in_array($data['estatus'] ?? '', ['activo', 'inactivo', 'agotado'], true)) {
            $errors[] = 'El estatus debe ser activo, inactivo o agotado.';
        }

        if (!empty($files['imagenes']['name'][0])) {
            foreach ($files['imagenes']['name'] as $index => $fileName) {
                $errorCode = $files['imagenes']['error'][$index] ?? UPLOAD_ERR_NO_FILE;
                if ($errorCode === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($errorCode !== UPLOAD_ERR_OK) {
                    $errors[] = 'No se pudo subir una de las imágenes seleccionadas.';
                    continue;
                }

                $size = (int) ($files['imagenes']['size'][$index] ?? 0);
                if ($size <= 0 || $size > MAX_UPLOAD_SIZE) {
                    $errors[] = 'Cada imagen debe pesar máximo 5 MB.';
                }
            }
        }

        $existingImageIds = array_values(array_filter(array_map('intval', $data['existing_image_ids'] ?? [])));
        $hasNewImages = !empty($files['imagenes']['name'][0]);
        if (empty($existingImageIds) && !$hasNewImages) {
            $errors[] = 'Debes conservar o cargar al menos una imagen para el producto.';
        }

        return $errors;
    }

    public function prepareProductData(array $postData): array
    {
        $contenidoNetoValor = trim($postData['contenido_neto_valor'] ?? '');
        $contenidoNetoUnidad = trim($postData['contenido_neto_unidad'] ?? 'mg');
        $contenidoNeto = $contenidoNetoValor !== '' ? str_replace(',', '.', $contenidoNetoValor) . ' ' . $contenidoNetoUnidad : null;
        $cantidadEnvase = trim($postData['cantidad_unidades'] ?? ($postData['cantidad_envase'] ?? ''));

        return [
            'nombre' => trim($postData['nombre'] ?? ''),
            'slug' => strtolower(trim($postData['slug'] ?? '')),
            'sku' => trim($postData['sku'] ?? ''),
            'precio' => (float) ($postData['precio'] ?? 0),
            'precio_descuento' => !empty($postData['precio_descuento']) ? (float) $postData['precio_descuento'] : null,
            'descripcion_corta' => trim($postData['descripcion_corta'] ?? ''),
            'descripcion_larga' => trim($postData['descripcion_larga'] ?? ''),
            'modo_empleo' => trim($postData['modo_empleo'] ?? ''),
            'usos' => trim($postData['usos'] ?? ''),
            'beneficios' => trim($postData['beneficios'] ?? ''),
            'contenido_neto' => $contenidoNeto,
            'cantidad_envase' => $cantidadEnvase,
            'destacado' => isset($postData['destacado']) ? 1 : 0,
            'categoria_id' => $this->resolveCategoryId($postData),
            'forma_id' => $this->resolveFormId($postData),
            'estatus' => $postData['estatus'] ?? 'activo',
            'existing_image_ids' => array_values(array_filter(array_map('intval', $postData['existing_image_ids'] ?? []))),
            'image_order' => array_values(array_filter(array_map('trim', $postData['image_order'] ?? []))),
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
            if (is_numeric($item)) {
                $ids[] = (int) $item;
                continue;
            }

            if (strpos($item, 'new:') === 0) {
                $name = trim(substr($item, 4));
                if ($name === '') {
                    continue;
                }

                if ($type === 'etiqueta') {
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
            if (strpos($item, 'new:') === 0) {
                $label = trim(substr($item, 4));
                if ($label === '') {
                    continue;
                }

                $payload[] = ['id' => $item, 'nombre' => $label];
                continue;
            }

            if (is_numeric($item)) {
                $id = (int) $item;
                foreach ($available as $entry) {
                    if (isset($entry['id']) && (int) $entry['id'] === $id) {
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
        if (!$product) {
            return null;
        }

        $product['etiquetas'] = Etiqueta::forProduct($id);
        $product['images'] = Producto::images($id);

        return $product;
    }

    public function saveProduct(array $productData, array $relationIds, array $files = [], ?int $existingId = null): int
    {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            if ($existingId !== null) {
                Producto::update($existingId, $productData);
                $productId = $existingId;
            } else {
                $productId = Producto::create($productData);
            }

            Producto::syncEtiquetas($productId, $relationIds['etiquetas']);
            Producto::syncImages($productId, $productData, $files['imagenes'] ?? []);

            $db->commit();
            return $productId;
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $message = $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'No fue posible guardar el producto. Intenta nuevamente.';

            throw new RuntimeException($message, 0, $exception);
        }
    }
}
