<?php
// views/admin/attributes/index.php
$title = 'GestiÃ³n de Atributos | Biomedcs Souls';
?>

<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>

        <div class="col-xl-9">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="display-6 fw-bold mb-1">GestiÃ³n de Atributos</h1>
                    <p class="text-muted mb-0">Administra etiquetas y categorÃ­as de productos.</p>
                </div>
                <a href="<?= site_url('admin/dashboard'); ?>" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>

            <div class="row g-4">

                <!-- GestiÃ³n de Etiquetas -->
                <div class="col-md-6 col-lg-5">
                    <div class="card-modern p-4 bg-white h-100">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-tags fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Etiquetas Visuales</h5>
                                <p class="text-muted small mb-0">Badges que aparecen en las tarjetas de productos</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary fs-6 px-3 py-2"><?= $etiquetasCount ?? 0; ?> etiquetas</span>
                            <a href="<?= site_url('admin/atributos/etiquetas'); ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Gestionar Etiquetas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GestiÃ³n de CategorÃ­as -->
                <div class="col-md-6 col-lg-5">
                    <div class="card-modern p-4 bg-white h-100">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-folder fa-2x text-info"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">CategorÃ­as</h5>
                                <p class="text-muted small mb-0">Organiza tus productos por categorÃ­as</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary fs-6 px-3 py-2"><?= $categoriesCount ?? 0; ?> categorÃ­as</span>
                            <a href="<?= site_url('admin/categorias'); ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Gestionar CategorÃ­as
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- InformaciÃ³n del Sistema -->
            <div class="card-modern p-4 mt-5 bg-white">
                <h5 class="mb-3">InformaciÃ³n del Sistema</h5>
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="display-6 fw-bold text-primary mb-1"><?= intval($productCount ?? 0); ?></div>
                        <small class="text-muted">Productos totales</small>
                    </div>
                    <div class="col-md-4">
                        <div class="display-6 fw-bold text-success mb-1"><?= intval($etiquetasCount ?? 0); ?></div>
                        <small class="text-muted">Etiquetas</small>
                    </div>
                    <div class="col-md-4">
                        <div class="display-6 fw-bold text-info mb-1"><?= intval($categoriesCount ?? 0); ?></div>
                        <small class="text-muted">CategorÃ­as</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>