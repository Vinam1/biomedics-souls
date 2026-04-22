<?php
// views/admin/categories/form.php

$isEdit = !empty($category);
$title = $isEdit ? 'Editar CategorÃ­a' : 'Nueva CategorÃ­a';

$nombre = $isEdit ? htmlspecialchars($category['nombre'] ?? '') : '';
$slug   = $isEdit ? htmlspecialchars($category['slug'] ?? '') : '';
?>

<div class="container py-5 admin-form" style="min-height: 100vh; background: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">

            <div class="card rounded-5 shadow-sm border-0">
                <div class="card-body p-5 p-lg-6">

                    <!-- Header -->
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-5">
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= site_url('admin/categorias'); ?>"
                               class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center"
                               style="width:52px; height:52px; font-size:1.5rem;">â†</a>
                            <div>
                                <h1 class="h2 fw-bold mb-1"><?= htmlspecialchars($title); ?></h1>
                                <p class="text-muted mb-0">Completa la informaciÃ³n de la categorÃ­a</p>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary px-4 py-2 rounded-pill">
                            <i class="fas fa-folder me-2"></i>CategorÃ­a
                        </span>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-4 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" class="row g-4">
                        <?= csrf_input(); ?>

                        <!-- InformaciÃ³n BÃ¡sica -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <i class="fas fa-folder text-info fs-3"></i>
                                        <h2 class="h5 fw-semibold mb-0">InformaciÃ³n de la CategorÃ­a</h2>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Nombre de la CategorÃ­a <span class="text-danger">*</span></label>
                                            <input type="text" name="nombre" class="form-control rounded-4"
                                                   placeholder="Ej: NootrÃ³picos, Vitaminas, AdaptÃ³genos" required
                                                   value="<?= $nombre; ?>"
                                                   onchange="updateSlug(this.value)">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                                            <input type="text" name="slug" id="slug" class="form-control rounded-4"
                                                   placeholder="nootropicos" required
                                                   value="<?= $slug; ?>">
                                            <small class="text-muted">Se genera automÃ¡ticamente desde el nombre (solo letras, nÃºmeros y guiones)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acciÃ³n -->
                        <div class="col-12 text-end pt-3">
                            <a href="<?= site_url('admin/categorias'); ?>"
                               class="btn btn-lg btn-outline-secondary rounded-4 px-5 me-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-lg btn-gradient rounded-4 px-5">
                                <i class="fas fa-save me-2"></i>
                                <?= $isEdit ? 'Actualizar CategorÃ­a' : 'Crear CategorÃ­a'; ?>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Generar slug automÃ¡ticamente desde el nombre
    function updateSlug(nombre) {
        const slug = nombre
            .toLowerCase()
            .trim()
            .replace(/[Ã¡Ã Ã¤Ã¢]/g, 'a')
            .replace(/[Ã©Ã¨Ã«Ãª]/g, 'e')
            .replace(/[Ã­Ã¬Ã¯Ã®]/g, 'i')
            .replace(/[Ã³Ã²Ã¶Ã´]/g, 'o')
            .replace(/[ÃºÃ¹Ã¼Ã»]/g, 'u')
            .replace(/[Ã±]/g, 'n')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');

        document.getElementById('slug').value = slug;
    }
</script>

<link rel="stylesheet" href="<?= asset_url('css/admin-attributes.css'); ?>">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
