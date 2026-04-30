<?php
// views/admin/attributes/index.php
$title = 'Gesti&oacute;n de Atributos | Biomedics Souls';
?>

<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>

        <div class="col-xl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="display-6 fw-bold mb-1">Gesti&oacute;n de Atributos</h1>
                    <p class="text-muted mb-0">Administra etiquetas y categor&iacute;as de productos.</p>
                </div>
                <a href="<?= site_url('admin/dashboard'); ?>" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-5">
                    <div class="card-modern p-4 bg-white h-100">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-tags fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Etiquetas visuales</h5>
                                <p class="text-muted small mb-0">Badges que aparecen en las tarjetas de productos</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary fs-6 px-3 py-2"><?= $etiquetasCount ?? 0; ?> etiquetas</span>
                            <a href="<?= site_url('admin/atributos/etiquetas'); ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Gestionar etiquetas
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5">
                    <div class="card-modern p-4 bg-white h-100">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-folder fa-2x text-info"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Categor&iacute;as</h5>
                                <p class="text-muted small mb-0">Organiza tus productos por categor&iacute;as</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary fs-6 px-3 py-2"><?= $categoriesCount ?? 0; ?> categor&iacute;as</span>
                            <a href="<?= site_url('admin/categorias'); ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Gestionar categor&iacute;as
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-modern p-4 mt-5 bg-white">
                <h5 class="mb-3">Informaci&oacute;n del sistema</h5>
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
                        <small class="text-muted">Categor&iacute;as</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
