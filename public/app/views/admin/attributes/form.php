<?php
// views/admin/attributes/form.php
$typeConfig = [
        'etiquetas' => ['singular' => 'Etiqueta', 'icon' => 'fa-tags',     'color' => 'text-warning'],
        'usos'      => ['singular' => 'Uso',      'icon' => 'fa-lightbulb','color' => 'text-info'],
        'beneficios'=> ['singular' => 'Beneficio','icon' => 'fa-heart',    'color' => 'text-danger'],
];

$config = $typeConfig[$type] ?? [];
$isEdit = !empty($item);

$nombre     = $isEdit ? htmlspecialchars($item['nombre'])           : '';
$slug       = $isEdit ? htmlspecialchars($item['slug'])             : '';
$color      = $isEdit ? htmlspecialchars($item['color'] ?? '#3B82F6') : '#3B82F6';
$descripcion= $isEdit ? htmlspecialchars($item['descripcion'] ?? '') : '';
$icono      = $isEdit ? htmlspecialchars($item['icono'] ?? 'fa-check') : 'fa-check';
?>

<div class="container py-5 admin-form" style="min-height: 100vh; background: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">

            <div class="card rounded-5 shadow-sm border-0">
                <div class="card-body p-5">

                    <!-- Encabezado -->
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-5">
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= site_url("admin/atributos/$type"); ?>"
                               class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center"
                               style="width:52px; height:52px; font-size:1.5rem;">←</a>
                            <div>
                                <h1 class="h2 fw-bold mb-1"><?= htmlspecialchars($title); ?></h1>
                                <p class="text-muted mb-0">Completa la información del <?= $config['singular'] ?? 'atributo'; ?></p>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary px-4 py-2 rounded-pill">
                            <i class="fas <?= $config['icon'] ?? 'fa-tag'; ?> me-2"></i><?= $config['singular'] ?? ''; ?>
                        </span>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-4 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" class="row g-4">
                        <?= csrf_input(); ?>

                        <!-- Información básica -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-info-circle text-primary fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">Información básica</h2>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">
                                                Nombre <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="nombre" class="form-control rounded-4"
                                                   placeholder="Ej: Antioxidante" required
                                                   value="<?= $nombre; ?>"
                                                   oninput="updateSlug(this.value)">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">
                                                Slug <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="slug" id="slug" class="form-control rounded-4"
                                                   placeholder="slug-automatico" required
                                                   value="<?= $slug; ?>">
                                            <small class="text-muted">Se genera automáticamente desde el nombre</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Color (solo etiquetas) -->
                        <?php if ($type === 'etiquetas'): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <i class="fas fa-palette text-info fs-3"></i>
                                            <h2 class="h5 fw-semibold mb-0">Estilos</h2>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Color</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="color" name="color" id="colorPicker"
                                                       class="form-control rounded-4"
                                                       style="max-width:100px; height:50px; cursor:pointer;"
                                                       value="<?= $color; ?>"
                                                       oninput="document.getElementById('colorText').value = this.value">
                                                <input type="text" id="colorText" class="form-control rounded-4"
                                                       placeholder="#3B82F6"
                                                       value="<?= $color; ?>" readonly>
                                                <small class="text-muted">Este color se mostrará en el frontend</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Descripción (usos / beneficios) -->
                        <?php if ($type === 'usos' || $type === 'beneficios'): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <i class="fas fa-file-alt text-info fs-3"></i>
                                            <h2 class="h5 fw-semibold mb-0">Descripción</h2>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">
                                                Descripción <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="descripcion" class="form-control rounded-4"
                                                      rows="4" required
                                                      placeholder="Describe brevemente este <?= $config['singular'] ?? 'atributo'; ?>..."><?= $descripcion; ?></textarea>
                                            <small class="text-muted">Esta descripción se mostrará en los detalles del producto</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Ícono (beneficios) -->
                        <?php if ($type === 'beneficios'): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <i class="fas fa-icons text-warning fs-3"></i>
                                            <h2 class="h5 fw-semibold mb-0">Ícono</h2>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold">Ícono de Font Awesome</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="fs-1" id="icon-preview">
                                                    <i class="fas <?= $icono; ?>"></i>
                                                </div>
                                                <input type="text" name="icono" class="form-control rounded-4"
                                                       placeholder="fa-heart, fa-star, fa-check..."
                                                       value="<?= $icono; ?>"
                                                       oninput="updateIconPreview(this.value)">
                                            </div>
                                            <small class="text-muted">
                                                Usa clases de Font Awesome (ej: fa-heart).
                                                <a href="https://fontawesome.com/icons" target="_blank">Ver íconos disponibles</a>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Acciones -->
                        <div class="col-12 text-end pt-3">
                            <a href="<?= site_url("admin/atributos/$type"); ?>"
                               class="btn btn-lg btn-outline-secondary rounded-4 px-5 me-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-lg btn-gradient rounded-4 px-5">
                                <i class="fas fa-save me-2"></i>
                                <?= $isEdit ? 'Actualizar' : 'Crear'; ?> <?= $config['singular'] ?? ''; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateSlug(value) {
        const slug = value
            .toLowerCase()
            .trim()
            .replace(/[áàäâ]/g, 'a')
            .replace(/[éèëê]/g, 'e')
            .replace(/[íìïî]/g, 'i')
            .replace(/[óòöô]/g, 'o')
            .replace(/[úùüû]/g, 'u')
            .replace(/[ñ]/g, 'n')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    }

    function updateIconPreview(iconClass) {
        const preview = document.getElementById('icon-preview');
        if (preview) {
            preview.innerHTML = '<i class="fas ' + iconClass.trim() + '"></i>';
        }
    }
</script>

<link rel="stylesheet" href="<?= asset_url('css/admin-attributes.css'); ?>">