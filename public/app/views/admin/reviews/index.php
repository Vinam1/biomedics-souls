<?php
$flashSuccess = $_SESSION['success'] ?? null;
$flashError = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-xl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">Reseñas</h1>
                    <p class="text-muted mb-0">Modera la visibilidad de las reseñas publicadas por los clientes.</p>
                </div>
                <a href="<?= site_url('admin/dashboard'); ?>" class="btn btn-outline-primary btn-lg">Volver al dashboard</a>
            </div>

            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success rounded-4"><?= htmlspecialchars($flashSuccess); ?></div>
            <?php endif; ?>
            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger rounded-4"><?= htmlspecialchars($flashError); ?></div>
            <?php endif; ?>

            <?php if (!empty($reviews)): ?>
                <div class="vstack gap-3">
                    <?php foreach ($reviews as $review): ?>
                        <?php $fullName = trim(($review['nombre'] ?? '') . ' ' . ($review['apellidos'] ?? '')); ?>
                        <div class="card-modern p-4 bg-white">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($review['producto_nombre'] ?? 'Producto'); ?></h5>
                                        <span class="badge <?= ($review['estatus'] ?? '') === 'publicada' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis'; ?>">
                                            <?= htmlspecialchars(ucfirst($review['estatus'] ?? '')); ?>
                                        </span>
                                    </div>
                                    <div class="text-warning small mb-2">
                                        <?php for ($i = 0; $i < (int) ($review['calificacion'] ?? 0); $i++): ?>
                                            <i class="bi bi-star-fill"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="text-muted small">
                                        Cliente:
                                        <a href="<?= site_url('admin/cliente-detalle/' . (int) ($review['cliente_id'] ?? 0)); ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($fullName !== '' ? $fullName : 'Cliente'); ?>
                                        </a>
                                        <?php if (!empty($review['email'])): ?>
                                            · <?= htmlspecialchars($review['email']); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted small">
                                        Fecha: <?= !empty($review['created_at']) ? date('d/m/Y H:i', strtotime($review['created_at'])) : 'N/D'; ?>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-start">
                                    <a href="<?= site_url('producto/' . ($review['producto_slug'] ?? '')); ?>" target="_blank" class="btn btn-sm btn-outline-dark">Ver producto</a>
                                    <form method="post" action="<?= site_url('admin/resena-estatus/' . (int) $review['id']); ?>">
                                        <?= csrf_input(); ?>
                                        <input type="hidden" name="estatus" value="<?= ($review['estatus'] ?? '') === 'publicada' ? 'eliminada' : 'publicada'; ?>">
                                        <button type="submit" class="btn btn-sm <?= ($review['estatus'] ?? '') === 'publicada' ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                            <?= ($review['estatus'] ?? '') === 'publicada' ? 'Ocultar' : 'Publicar'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <?php if (!empty($review['titulo'])): ?>
                                <div class="fw-semibold mb-2"><?= htmlspecialchars($review['titulo']); ?></div>
                            <?php endif; ?>

                            <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($review['comentario'] ?? '')); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info rounded-4">Todavía no hay reseñas registradas.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
