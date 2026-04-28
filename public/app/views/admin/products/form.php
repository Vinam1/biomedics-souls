<?php
$contenidoNetoValue = trim($product['contenido_neto'] ?? '');
$contenidoNetoCantidad = '';
$contenidoNetoUnidad = 'mg';

if ($contenidoNetoValue !== '') {
    if (preg_match('/^([0-9]+(?:[.,][0-9]+)?)\s*(.+)$/', $contenidoNetoValue, $matches)) {
        $contenidoNetoCantidad = str_replace(',', '.', $matches[1]);
        $contenidoNetoUnidad = trim($matches[2]);
    } else {
        $contenidoNetoCantidad = $contenidoNetoValue;
    }
}

$selectedCategory = $product['categoria_id'] ?? '';
$selectedForma = $product['forma_id'] ?? '';
$selectedStatus = $product['estatus'] ?? '';
$featuredChecked = !empty($product['destacado']) ? 'checked' : '';
$cantidadUnidades = $product['cantidad_envase'] ?? '';
$currentTags = $product['etiquetas'] ?? [];
$usos = $product['usos'] ?? '';
$beneficios = $product['beneficios'] ?? '';
$modoEmpleo = $product['modo_empleo'] ?? '';
$currentImages = $product['images'] ?? [];
$isEdit = !empty($product['id']);
?>

<div class="container py-5 admin-product-form" style="min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card rounded-5 shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-5">
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= site_url('admin/productos'); ?>"
                               class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center"
                               style="width:52px; height:52px; font-size:1.5rem;">&larr;</a>
                            <div>
                                <h1 class="h2 fw-bold mb-1"><?= htmlspecialchars($title); ?></h1>
                                <p class="text-muted mb-0">Captura la informacion del producto y valida imagenes, precios y SEO antes de guardar.</p>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary px-4 py-2 rounded-pill">
                            <?= $isEdit ? 'Edicion activa' : 'Nuevo registro'; ?>
                        </span>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-4 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div id="form-feedback" class="alert alert-danger rounded-4 d-none mb-4"></div>

                    <form method="post" action="" enctype="multipart/form-data" class="row g-4" id="product-form" novalidate>
                        <?= csrf_input(); ?>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-info-circle text-primary fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Informacion basica</h2>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label for="nombre" class="form-label fw-semibold">Nombre del producto</label>
                                            <input type="text"
                                                   id="nombre"
                                                   name="nombre"
                                                   class="form-control rounded-4"
                                                   placeholder="Ej: Vinagre de Manzana"
                                                   maxlength="150"
                                                   required
                                                   value="<?= htmlspecialchars($product['nombre'] ?? ''); ?>">
                                            <small class="text-muted">Nombre comercial visible en catalogo, carrito y detalle.</small>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="categoria_id" class="form-label fw-semibold">Categoria</label>
                                            <select id="categoria_id" name="categoria_id" class="form-select rounded-4" required>
                                                <option value="">Selecciona una categoria</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id']; ?>" <?= $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($category['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="forma_id" class="form-label fw-semibold">Presentacion</label>
                                            <select name="forma_id" id="forma_id" class="form-select rounded-4" required>
                                                <option value="">Selecciona presentacion</option>
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
                                            <label for="contenido_neto_valor" class="form-label fw-semibold">Contenido neto</label>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   id="contenido_neto_valor"
                                                   name="contenido_neto_valor"
                                                   class="form-control rounded-4"
                                                   placeholder="Cantidad"
                                                   required
                                                   value="<?= htmlspecialchars($contenidoNetoCantidad); ?>">
                                        </div>

                                        <div class="col-12 col-lg-3">
                                            <label for="contenido_neto_unidad" class="form-label fw-semibold">Unidad</label>
                                            <select id="contenido_neto_unidad" name="contenido_neto_unidad" class="form-select rounded-4" required>
                                                <?php foreach (['mg', 'g', 'ml', 'mcg', 'UI'] as $unit): ?>
                                                    <option value="<?= $unit; ?>" <?= $contenidoNetoUnidad === $unit ? 'selected' : ''; ?>>
                                                        <?= strtoupper($unit); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 col-lg-6" id="cantidad-envase-group">
                                            <label for="cantidad_unidades" class="form-label fw-semibold">
                                                Cantidad de unidades por envase
                                                <small class="text-muted">(opcional)</small>
                                            </label>
                                            <input type="text"
                                                   id="cantidad_unidades"
                                                   name="cantidad_unidades"
                                                   class="form-control rounded-4"
                                                   maxlength="50"
                                                   value="<?= htmlspecialchars($cantidadUnidades); ?>"
                                                   placeholder="Ej: 30 capsulas, 60 tabletas, 1 frasco">
                                            <small class="text-muted">Se recomienda para capsulas, tabletas y presentaciones por piezas.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-dollar-sign text-success fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Precios</h2>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="precio" class="form-label fw-semibold">Precio normal</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       id="precio"
                                                       name="precio"
                                                       class="form-control rounded-end-4"
                                                       required
                                                       value="<?= htmlspecialchars($product['precio'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="precio_descuento" class="form-label fw-semibold">Precio con descuento <small class="text-muted">(opcional)</small></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       id="precio_descuento"
                                                       name="precio_descuento"
                                                       class="form-control rounded-end-4"
                                                       value="<?= htmlspecialchars($product['precio_descuento'] ?? ''); ?>">
                                            </div>
                                            <small class="text-muted">Debe ser menor al precio normal.</small>
                                        </div>

                                        <div class="col-12">
                                            <div class="rounded-4 bg-light p-3 small text-muted">
                                                <strong class="d-block text-dark mb-1">Resumen rapido</strong>
                                                El precio final mostrado al cliente sera el descuento si existe; si no, se usa el precio normal.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-file-alt text-info fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Descripcion</h2>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descripcion_corta" class="form-label fw-semibold">Descripcion corta</label>
                                        <textarea id="descripcion_corta" name="descripcion_corta" class="form-control rounded-4" rows="3" maxlength="300" placeholder="Resumen breve para tarjetas y listados."><?= htmlspecialchars($product['descripcion_corta'] ?? ''); ?></textarea>
                                    </div>

                                    <div>
                                        <label for="descripcion_larga" class="form-label fw-semibold">Descripcion larga</label>
                                        <textarea id="descripcion_larga" name="descripcion_larga" class="form-control rounded-4" rows="6" placeholder="Explica composicion, beneficios y diferencias del producto."><?= htmlspecialchars($product['descripcion_larga'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-list-ul text-warning fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Atributos y detalles</h2>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Etiquetas visuales</h6>
                                            <div class="input-group mb-2">
                                                <select id="select-etiqueta" class="form-select rounded-start-4">
                                                    <option value="">Selecciona etiqueta</option>
                                                    <?php foreach ($etiquetasDisponibles as $tag): ?>
                                                        <option value="<?= $tag['id']; ?>"><?= htmlspecialchars($tag['nombre']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="button" class="btn btn-primary px-3 rounded-end-4" onclick="agregarNuevoTag('etiqueta')">
                                                    Anadir
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline-secondary ms-1 rounded-4"
                                                        onclick="openQuickAddModal('etiqueta')"
                                                        title="Crear nueva etiqueta">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                Se muestran como badges en tarjetas y detalle.
                                                <a href="<?= site_url('admin/atributos/etiquetas'); ?>" class="text-primary fw-semibold" target="_blank">Gestionar &rarr;</a>
                                            </small>
                                            <div id="etiquetas-container" class="d-flex flex-wrap gap-2"></div>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Modo de empleo</h6>
                                            <textarea id="modo_empleo" name="modo_empleo" class="form-control rounded-4" rows="4" placeholder="Instrucciones de uso claras y puntuales."><?= htmlspecialchars($modoEmpleo); ?></textarea>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Usos o aplicaciones</h6>
                                            <textarea id="usos" name="usos" class="form-control rounded-4" rows="4" placeholder="Ej: articulaciones, piel, cabello, energia..."><?= htmlspecialchars($usos); ?></textarea>
                                            <small class="text-muted">Describe para que sirve el producto.</small>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <h6 class="fw-semibold text-muted mb-3">Beneficios principales</h6>
                                            <textarea id="beneficios" name="beneficios" class="form-control rounded-4" rows="4" placeholder="Escribe un beneficio por linea para mostrarlos mejor en frontend."><?= htmlspecialchars($beneficios); ?></textarea>
                                            <small class="text-muted">Una linea por beneficio mejora la presentacion en el detalle.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-search text-primary fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">SEO e identificadores</h2>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label for="sku" class="form-label fw-semibold">SKU</label>
                                            <input type="text"
                                                   id="sku"
                                                   name="sku"
                                                   class="form-control rounded-4"
                                                   maxlength="50"
                                                   required
                                                   <?= $isEdit ? 'readonly' : ''; ?>
                                                   value="<?= htmlspecialchars($product['sku'] ?? ''); ?>">
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="slug" class="form-label fw-semibold">Slug</label>
                                            <input type="text"
                                                   id="slug"
                                                   name="slug"
                                                   class="form-control rounded-4"
                                                   maxlength="150"
                                                   pattern="[a-z0-9-]+"
                                                   required
                                                   <?= $isEdit ? 'readonly' : ''; ?>
                                                   value="<?= htmlspecialchars($product['slug'] ?? ''); ?>">
                                            <small class="text-muted">Solo letras minusculas, numeros y guiones.<?= $isEdit ? ' (No editable en edición)' : ' Se autogenera desde el nombre si no lo editas manualmente.'; ?></small>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="estatus" class="form-label fw-semibold">
                                                Estatus
                                                <small class="text-muted">(opcional)</small>
                                            </label>
                                            <input type="text"
                                                   id="estatus"
                                                   name="estatus"
                                                   class="form-control rounded-4"
                                                   maxlength="100"
                                                   value="<?= htmlspecialchars($selectedStatus); ?>"
                                                   placeholder="Ej: Agotado, PrÃ³ximamente, Preventa">
                                            <small class="text-muted">Si escribes exactamente "agotado", el producto no podrÃ¡ agregarse al carrito.</small>
                                        </div>

                                        <div class="col-12 col-lg-6 d-flex align-items-center pt-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1" <?= $featuredChecked; ?>>
                                                <label class="form-check-label fw-semibold" for="destacado">
                                                    Producto destacado
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-4 border-top">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <i class="fas fa-images text-primary fs-3"></i>
                                            <h2 class="h5 fw-semibold mb-0">Imagenes del producto</h2>
                                        </div>

                                        <input type="file" name="imagenes[]" id="imagenesInput" class="form-control rounded-4 mb-3" accept="image/*" multiple>
                                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                                            <p class="text-muted mb-0">
                                                Puedes arrastrar para reordenar. La primera imagen se guardara como principal.
                                            </p>
                                            <small class="text-muted">Formatos: JPG, PNG, WEBP, GIF. Maximo 5 MB por imagen.</small>
                                        </div>

                                        <div id="image-manager" class="image-manager">
                                            <div id="image-empty-state" class="image-empty-state <?= !empty($currentImages) ? 'd-none' : ''; ?>">
                                                <i class="fas fa-images mb-3"></i>
                                                <div class="fw-semibold">Aun no hay imagenes cargadas</div>
                                                <div class="small text-muted">Selecciona archivos y ordenalos aqui.</div>
                                            </div>

                                            <div id="preview-container"
                                                 class="image-grid"
                                                 data-existing-images="<?= htmlspecialchars(json_encode(array_map(
                                                     static function ($img) {
                                                         return [
                                                             'id' => (int) ($img['id'] ?? 0),
                                                             'url' => asset_url('img/products/' . ($img['url_imagen'] ?? '')),
                                                             'filename' => $img['url_imagen'] ?? '',
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
                                                            <div class="image-card__name" title="<?= htmlspecialchars($image['url_imagen'] ?? 'Imagen actual'); ?>">
                                                                <?= htmlspecialchars($image['url_imagen'] ?? 'Imagen actual'); ?>
                                                            </div>
                                                            <div class="image-card__meta">Guardada en el servidor</div>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="existing_image_ids[]" value="<?= (int) $image['id']; ?>" data-image-input="1">
                                                    <input type="hidden" name="image_order[]" value="existing:<?= (int) $image['id']; ?>" data-image-input="1">
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-3 pt-3">
                            <a href="<?= site_url('admin/productos'); ?>" class="btn btn-outline-secondary btn-lg rounded-4 px-4">Cancelar</a>
                            <button type="submit" class="btn btn-lg px-5 rounded-4 btn-gradient">
                                <i class="fas fa-save me-2"></i>
                                <?= $isEdit ? 'Actualizar producto' : 'Agregar producto'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
                                <input type="text" class="form-control rounded-4" id="attribute-name" placeholder="Ej: Antioxidante" required>
                            </div>
                            <div class="col-12" id="color-field" style="display:none;">
                                <label class="form-label fw-semibold">Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" class="form-control rounded-4" id="attribute-color" value="#3B82F6" style="max-width:100px; height:50px;">
                                    <input type="text" class="form-control rounded-4" id="color-text" value="#3B82F6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-4 px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-gradient rounded-4 px-4" id="save-attribute-btn" disabled>
                    <i class="fas fa-save me-2"></i>Crear y agregar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.productoFormData = {
        currentTags: <?= json_encode($currentTags); ?>,
        currentImages: <?= json_encode(array_map(static function ($img) {
            return [
                'id' => (int) ($img['id'] ?? 0),
                'url' => asset_url('img/products/' . ($img['url_imagen'] ?? '')),
                'filename' => $img['url_imagen'] ?? '',
                'principal' => !empty($img['es_principal']),
            ];
        }, $currentImages)); ?>,
        csrfToken: <?= json_encode($csrfToken ?? csrf_token()); ?>,
        endpoints: {
            add: "<?= site_url('admin/atributo-agregar'); ?>",
            update: "<?= site_url('admin/atributo-actualizar'); ?>"
        }
    };
</script>

<link rel="stylesheet" href="<?= asset_url('css/admin-product-form.css'); ?>">
<script src="<?= asset_url('js/producto-form.js'); ?>"></script>
