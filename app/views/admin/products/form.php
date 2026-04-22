<?php
// views/admin/products/form.php

// ==================== PROCESAMIENTO INICIAL ====================
$contenidoNetoValue   = trim($product['contenido_neto'] ?? '');
$contenidoNetoCantidad = '';
$contenidoNetoUnidad  = 'mg';

if ($contenidoNetoValue !== '') {
    if (preg_match('/^([0-9]+(?:[.,][0-9]+)?)\s*(.+)$/', $contenidoNetoValue, $matches)) {
        $contenidoNetoCantidad = str_replace(',', '.', $matches[1]);
        $contenidoNetoUnidad   = trim($matches[2]);
    } else {
        $contenidoNetoCantidad = $contenidoNetoValue;
    }
}

$selectedCategory = $product['categoria_id'] ?? '';
$selectedForma    = $product['forma_id']     ?? '';
$selectedStatus   = $product['estatus']      ?? 'activo';
$featuredChecked  = !empty($product['destacado']) ? 'checked' : '';

// BUG FIX: the DB column and model are 'cantidad_envase'; reading 'cantidad_unidades' here
// would always be empty when editing an existing product.
$cantidadUnidades = $product['cantidad_envase'] ?? '';

$currentTags   = $product['etiquetas']  ?? [];
$usos          = $product['usos']       ?? '';
$beneficios    = $product['beneficios'] ?? '';
$modoEmpleo    = $product['modo_empleo'] ?? '';
$currentImages = $product['images']     ?? [];
?>

<div class="container py-5 admin-product-form" style="min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="card rounded-5 shadow-sm border-0">
                <div class="card-body p-5">

                    <!-- Encabezado -->
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-5">
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= site_url('admin/productos'); ?>"
                               class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center"
                               style="width:52px; height:52px; font-size:1.5rem;">←</a>
                            <div>
                                <h1 class="h2 fw-bold mb-1"><?= htmlspecialchars($title); ?></h1>
                                <p class="text-muted mb-0">Completa la información detallada del producto.</p>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary px-4 py-2 rounded-pill">Panel de producto</span>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" enctype="multipart/form-data" class="row g-4">
                        <?= csrf_input(); ?>

                        <!-- 1. Información Básica -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-info-circle text-primary fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Información básica</h2>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">Nombre del producto</label>
                                            <input type="text" name="nombre" class="form-control rounded-4"
                                                   placeholder="Ej: Vinagre de Manzana" required
                                                   value="<?= htmlspecialchars($product['nombre'] ?? ''); ?>">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">Categoría</label>
                                            <select name="categoria_id" class="form-select rounded-4" required>
                                                <option value="">Selecciona una categoría</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id']; ?>"
                                                            <?= $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($category['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">Presentación</label>
                                            <select name="forma_id" id="forma_id" class="form-select rounded-4" required>
                                                <option value="">Selecciona presentación</option>
                                                <?php foreach ($formas as $forma): ?>
                                                    <option value="<?= $forma['id']; ?>"
                                                            data-nombre="<?= htmlspecialchars($forma['nombre']); ?>"
                                                            <?= $selectedForma == $forma['id'] ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($forma['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <label class="form-label fw-semibold">Contenido neto</label>
                                            <input type="number" step="0.01" name="contenido_neto_valor"
                                                   class="form-control rounded-4" placeholder="Cantidad"
                                                   value="<?= htmlspecialchars($contenidoNetoCantidad); ?>" required>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <label class="form-label fw-semibold">Unidad</label>
                                            <select name="contenido_neto_unidad" class="form-select rounded-4" required>
                                                <?php foreach (['mg','g','ml','mcg','UI'] as $unit): ?>
                                                    <option value="<?= $unit; ?>"
                                                            <?= $contenidoNetoUnidad === $unit ? 'selected' : ''; ?>>
                                                        <?= strtoupper($unit); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">
                                                Cantidad de unidades por envase
                                                <small class="text-muted">(opcional)</small>
                                            </label>
                                            <!-- NOTE: name="cantidad_unidades" — ProductService reads this key -->
                                            <input type="text" name="cantidad_unidades"
                                                   class="form-control rounded-4"
                                                   value="<?= htmlspecialchars($cantidadUnidades); ?>"
                                                   placeholder="Ej: 30 cápsulas, 60 tabletas, 1 frasco">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Precios -->
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-dollar-sign text-success fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Precios</h2>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Precio normal</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="precio"
                                                       class="form-control rounded-end-4" required
                                                       value="<?= htmlspecialchars($product['precio'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Precio con descuento <small class="text-muted">(opcional)</small></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="precio_descuento"
                                                       class="form-control rounded-end-4"
                                                       value="<?= htmlspecialchars($product['precio_descuento'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Descripción -->
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-file-alt text-info fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Descripción</h2>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Descripción corta</label>
                                        <textarea name="descripcion_corta" class="form-control rounded-4" rows="3"><?= htmlspecialchars($product['descripcion_corta'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold">Descripción larga</label>
                                        <textarea name="descripcion_larga" class="form-control rounded-4" rows="5"><?= htmlspecialchars($product['descripcion_larga'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Atributos y Detalles -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-list-ul text-warning fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Atributos y Detalles</h2>
                                    </div>
                                    <div class="row g-4">

                                        <!-- Etiquetas -->
                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Etiquetas visuales</h6>
                                            <div class="input-group mb-2">
                                                <select id="select-etiqueta" class="form-select rounded-start-4">
                                                    <option value="">Selecciona etiqueta</option>
                                                    <?php foreach ($etiquetasDisponibles as $tag): ?>
                                                        <option value="<?= $tag['id']; ?>"><?= htmlspecialchars($tag['nombre']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="button"
                                                        class="btn btn-primary px-3 rounded-end-4"
                                                        onclick="agregarNuevoTag('etiqueta')">
                                                    Añadir
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline-secondary ms-1 rounded-4"
                                                        onclick="openQuickAddModal('etiqueta')"
                                                        title="Crear nueva etiqueta">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                Badges que aparecen en la tarjeta del producto.
                                                <a href="<?= site_url('admin/atributos/etiquetas'); ?>"
                                                   class="text-primary fw-semibold" target="_blank">
                                                    Gestionar →
                                                </a>
                                            </small>
                                            <div id="etiquetas-container" class="d-flex flex-wrap gap-2"></div>
                                        </div>

                                        <!-- Modo de Empleo -->
                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Modo de empleo</h6>
                                            <textarea name="modo_empleo" class="form-control rounded-4" rows="4"
                                                      placeholder="Instrucciones de uso..."><?= htmlspecialchars($modoEmpleo); ?></textarea>
                                        </div>

                                        <!-- Usos -->
                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Usos o aplicaciones</h6>
                                            <textarea name="usos" class="form-control rounded-4" rows="4"
                                                      placeholder="Ej: Articulaciones, piel, cabello..."><?= htmlspecialchars($usos); ?></textarea>
                                            <small class="text-muted">Describe para qué sirve el producto.</small>
                                        </div>

                                        <!-- Beneficios -->
                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Beneficios principales</h6>
                                            <textarea name="beneficios" class="form-control rounded-4" rows="4"
                                                      placeholder="Ej: Reduce el dolor articular, mejora movilidad..."><?= htmlspecialchars($beneficios); ?></textarea>
                                            <small class="text-muted">Enumera las ventajas de consumir este producto.</small>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. SEO + Imágenes -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-search text-primary fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">SEO e identificadores</h2>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">SKU</label>
                                            <input type="text" name="sku" class="form-control rounded-4" required
                                                   value="<?= htmlspecialchars($product['sku'] ?? ''); ?>">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">Slug</label>
                                            <input type="text" name="slug" class="form-control rounded-4" required
                                                   value="<?= htmlspecialchars($product['slug'] ?? ''); ?>">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label fw-semibold">Estatus</label>
                                            <select name="estatus" class="form-select rounded-4">
                                                <?php foreach ($statusOptions as $value => $label): ?>
                                                    <option value="<?= $value; ?>"
                                                            <?= $selectedStatus === $value ? 'selected' : ''; ?>>
                                                        <?= $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-6 d-flex align-items-center pt-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       id="destacado" name="destacado" value="1"
                                                        <?= $featuredChecked; ?>>
                                                <label class="form-check-label fw-semibold" for="destacado">
                                                    Producto destacado
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Imágenes -->
                                    <div class="mt-5 pt-4 border-top">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <i class="fas fa-images text-primary fs-3"></i>
                                            <h2 class="h5 fw-semibold mb-0">Imágenes del producto</h2>
                                        </div>
                                        <input type="file" name="imagenes[]" id="imagenesInput"
                                               class="form-control rounded-4 mb-3" accept="image/*" multiple>
                                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                                            <p class="text-muted mb-0">
                                                Las imágenes se muestran al seleccionarlas, puedes reordenarlas arrastrando
                                                y la primera posición será la principal.
                                            </p>
                                            <small class="text-muted">Formatos: JPG, PNG, WEBP, GIF. Máximo 5 MB c/u.</small>
                                        </div>

                                        <div id="image-manager" class="image-manager">
                                            <div id="image-empty-state"
                                                 class="image-empty-state <?= !empty($currentImages) ? 'd-none' : ''; ?>">
                                                <i class="fas fa-images mb-3"></i>
                                                <div class="fw-semibold">Aún no hay imágenes cargadas</div>
                                                <div class="small text-muted">Selecciona archivos y ordénalos aquí.</div>
                                            </div>
                                            <div id="preview-container"
                                                 class="image-grid"
                                                 data-existing-images="<?= htmlspecialchars(json_encode(array_map(
                                                         static function ($img) {
                                                             return [
                                                                     'id'        => (int) ($img['id'] ?? 0),
                                                                     'url'       => asset_url('img/products/' . ($img['url_imagen'] ?? '')),
                                                                     'filename'  => $img['url_imagen'] ?? '',
                                                                     'principal' => !empty($img['es_principal']),
                                                             ];
                                                         },
                                                         $currentImages
                                                 )), ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php foreach ($currentImages as $index => $image): ?>
                                                    <div class="image-card image-card--static">
                                                        <div class="image-card__handle" title="Arrastra para reordenar">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </div>
                                                        <?php if ($index === 0): ?>
                                                            <span class="image-badge">Principal</span>
                                                        <?php endif; ?>
                                                        <div class="image-card__frame">
                                                            <img src="<?= htmlspecialchars(asset_url('img/products/' . ($image['url_imagen'] ?? ''))); ?>"
                                                                 alt="<?= htmlspecialchars($image['url_imagen'] ?? 'Imagen del producto'); ?>"
                                                                 class="image-card__img">
                                                        </div>
                                                        <div class="image-card__body">
                                                            <div class="image-card__name"
                                                                 title="<?= htmlspecialchars($image['url_imagen'] ?? 'Imagen actual'); ?>">
                                                                <?= htmlspecialchars($image['url_imagen'] ?? 'Imagen actual'); ?>
                                                            </div>
                                                            <div class="image-card__meta">Guardada en el servidor</div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Guardar -->
                        <div class="col-12 text-end pt-3">
                            <button type="submit" class="btn btn-lg px-5 rounded-4 btn-gradient">
                                <i class="fas fa-save me-2"></i>
                                <?= $title === 'Editar producto' ? 'Actualizar producto' : 'Agregar producto'; ?>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar etiquetas rápidamente -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="quickAddModalLabel">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Nueva etiqueta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form id="quickAddForm">
                    <input type="hidden" id="modal-attribute-type" value="">
                    <div id="dynamic-fields" class="d-none">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" id="name-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control rounded-4"
                                       id="attribute-name" placeholder="Ej: Antioxidante" required>
                            </div>
                            <div class="col-12" id="color-field" style="display:none;">
                                <label class="form-label fw-semibold">Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" class="form-control rounded-4"
                                           id="attribute-color" value="#3B82F6"
                                           style="max-width:100px; height:50px;">
                                    <input type="text" class="form-control rounded-4"
                                           id="color-text" value="#3B82F6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-4 px-4"
                        data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-gradient rounded-4 px-4"
                        id="save-attribute-btn" disabled>
                    <i class="fas fa-save me-2"></i>Crear y agregar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.productoFormData = {
        currentTags:   <?= json_encode($currentTags); ?>,
        currentImages: <?= json_encode(array_map(static function ($img) {
            return [
                    'id'        => (int) ($img['id'] ?? 0),
                    'url'       => asset_url('img/products/' . ($img['url_imagen'] ?? '')),
                    'filename'  => $img['url_imagen'] ?? '',
                    'principal' => !empty($img['es_principal']),
            ];
        }, $currentImages)); ?>,
        csrfToken: <?= json_encode($csrfToken ?? csrf_token()); ?>,
        endpoints: {
            add:    "<?= site_url('admin/atributo-agregar'); ?>",
            update: "<?= site_url('admin/atributo-actualizar'); ?>"
        }
    };
</script>

<link rel="stylesheet" href="<?= asset_url('css/admin-product-form.css'); ?>">
<script src="<?= asset_url('js/producto-form.js'); ?>"></script>