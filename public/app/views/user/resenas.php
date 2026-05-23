<?php
$reviews = $reviews ?? [];
$reviewableProducts = $reviewableProducts ?? [];
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Mis reseñas</h2>
        <p class="text-muted mb-0">Consulta lo que ya reseñaste y los productos que ya puedes calificar.</p>
    </div>
    <a href="<?= site_url('cuenta/pedidos'); ?>" class="btn btn-outline-primary rounded-pill">Ver mis pedidos</a>
</div>

<div class="row g-4">
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Productos listos para reseñar</h5>
                <?php if (!empty($reviewableProducts)): ?>
                    <div class="vstack gap-3">
                        <?php foreach ($reviewableProducts as $product): ?>
                            <div class="border rounded-4 p-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($product['nombre'] ?? 'Producto'); ?></div>
                                        <div class="text-muted small">SKU: <?= htmlspecialchars($product['sku'] ?? 'N/D'); ?></div>
                                        <div class="text-muted small">
                                            Compra entregada:
                                            <?= !empty($product['ultima_compra_fecha']) ? date('d/m/Y', strtotime($product['ultima_compra_fecha'])) : 'N/D'; ?>
                                        </div>
                                    </div>
                                    <a href="<?= site_url('producto/' . ($product['slug'] ?? '')) . '#escribir-resena'; ?>" class="btn btn-sm btn-primary rounded-pill">
                                        Reseñar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border rounded-4 mb-0">
                        Aún no tienes productos elegibles para reseñar. Se habilitan cuando el pedido aparece como entregado.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Reseñas publicadas</h5>
                <?php if (!empty($reviews)): ?>
                    <div class="vstack gap-3">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border rounded-4 p-3">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div>
                                        <a href="<?= site_url('producto/' . ($review['producto_slug'] ?? '')); ?>" class="fw-semibold text-decoration-none">
                                            <?= htmlspecialchars($review['producto_nombre'] ?? 'Producto'); ?>
                                        </a>
                                        <div class="text-warning small mt-1">
                                            <?php for ($i = 0; $i < (int) ($review['calificacion'] ?? 0); $i++): ?>
                                                <i class="bi bi-star-fill"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark"><?= htmlspecialchars(ucfirst($review['estatus'] ?? 'publicada')); ?></span>
                                        <div class="text-muted small mt-1">
                                            <?= !empty($review['created_at']) ? date('d/m/Y H:i', strtotime($review['created_at'])) : ''; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($review['titulo'])): ?>
                                    <div class="fw-semibold mb-1"><?= htmlspecialchars($review['titulo']); ?></div>
                                <?php endif; ?>

                                <p class="text-muted small mb-0"><?= nl2br(htmlspecialchars($review['comentario'] ?? '')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border rounded-4 mb-0">
                        Todavía no has publicado reseñas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
