<?php
$title = $title ?? ($product['name'] ?? 'Producto');
$galleryImages = array_values(array_filter($images ?? [], static fn ($image) => !empty($image['url_imagen'])));
$mainImage = !empty($galleryImages)
        ? asset_url('img/products/' . $galleryImages[0]['url_imagen'])
        : (!empty($product['image']) ? $product['image'] : null);
$displayPrice = (float) ($product['price'] ?? 0);
$statusLabel = trim((string) ($product['status'] ?? ''));
$isOutOfStock = !empty($product['is_out_of_stock']);
?>

<div class="container py-5">
    <nav class="breadcrumb-custom mb-4">
        <a href="<?= site_url(); ?>">Inicio</a>
        <span class="mx-2">/</span>
        <a href="<?= site_url('catalogo'); ?>">CatÃ¡logo</a>
        <?php if (!empty($product['category_slug']) && !empty($product['category'])): ?>
            <span class="mx-2">/</span>
            <a href="<?= site_url('category/' . $product['category_slug']); ?>"><?= htmlspecialchars($product['category']); ?></a>
        <?php endif; ?>
        <span class="mx-2">/</span>
        <span><?= htmlspecialchars($product['name'] ?? 'Producto'); ?></span>
    </nav>

    <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success rounded-4"><?= htmlspecialchars($flashSuccess); ?></div>
    <?php endif; ?>
    <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger rounded-4"><?= htmlspecialchars($flashError); ?></div>
    <?php endif; ?>

    <div class="row g-5 align-items-start">
        <div class="col-lg-6">
            <div class="section-card p-4 mb-4">
                <div class="rounded-4 overflow-hidden bg-white border mb-3">
                    <?php if ($mainImage): ?>
                        <img id="mainImage" src="<?= htmlspecialchars($mainImage); ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Producto'); ?>" class="img-fluid w-100" style="max-height: 520px; object-fit: cover;">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center text-muted" style="height: 520px;">Sin imagen disponible</div>
                    <?php endif; ?>
                </div>

                <?php if (count($galleryImages) > 1): ?>
                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($galleryImages as $image): ?>
                            <img src="<?= htmlspecialchars(asset_url('img/products/' . $image['url_imagen'])); ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Producto'); ?>" class="product-thumbnail">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <?php if (!empty($product['benefits'])): ?>
                        <h5 class="fw-bold text-uppercase small mb-3">Beneficios</h5>
                        <ul class="benefits-list list-unstyled">
                            <?php foreach ($product['benefits'] as $benefit): ?>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-circle-check text-success me-2 mt-1"></i>
                                    <span class="text-muted small"><?= htmlspecialchars($benefit); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="usage-box p-3 rounded-4" style="background-color: #f8f9fa; border-left: 4px solid #6f42c1;">
                        <h5 class="fw-bold small text-uppercase mb-2">Modo de empleo</h5>
                        <p class="mb-0 small text-muted">
                            <?= nl2br(htmlspecialchars($product['usage_instructions'] ?? 'Consulta a tu especialista antes de consumir este suplemento.')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php if ($statusLabel !== ''): ?>
                    <span class="badge rounded-pill px-3 py-2 <?= $isOutOfStock ? 'text-bg-danger' : 'text-bg-light border'; ?>">
                        <?= htmlspecialchars($statusLabel); ?>
                    </span>
                <?php endif; ?>
                <?php foreach ($badges ?? [] as $badge): ?>
                    <span class="badge rounded-pill px-3 py-2" style="background: <?= htmlspecialchars($badge['color'] ?? '#3B82F6'); ?>20; color: <?= htmlspecialchars($badge['color'] ?? '#3B82F6'); ?>;">
                        <?= htmlspecialchars($badge['nombre'] ?? 'Etiqueta'); ?>
                    </span>
                <?php endforeach; ?>
                <?php if (!empty($product['category'])): ?>
                    <span class="badge rounded-pill text-bg-light px-3 py-2 border"><?= htmlspecialchars($product['category']); ?></span>
                <?php endif; ?>
            </div>

            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($product['name'] ?? 'Producto'); ?></h1>

            <div class="d-flex flex-wrap align-items-center gap-4 mb-4">
                <div class="price-big mb-0 fw-bold">$<?= number_format($displayPrice, 2); ?></div>
                <div class="text-warning fw-semibold">
                    <?= str_repeat('â˜…', (int) round((float) ($product['rating'] ?? 0))); ?>
                    <span class="text-muted ms-2"><?= number_format((float) ($product['rating'] ?? 0), 1); ?> (<?= (int) ($product['reviews_count'] ?? 0); ?> reseÃ±as)</span>
                </div>
            </div>

            <?php if (!empty($product['short_description'])): ?>
                <p class="lead text-muted mb-4"><?= htmlspecialchars($product['short_description']); ?></p>
            <?php endif; ?>

            <div class="d-flex flex-wrap gap-3 mb-4">
                <?php if (!empty($product['content'])): ?>
                    <div class="product-badge-info p-2 px-3 border rounded-3 bg-white">
                        <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Contenido</small>
                        <span class="fw-bold"><?= htmlspecialchars($product['content']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($product['sku'])): ?>
                    <div class="product-badge-info p-2 px-3 border rounded-3 bg-white">
                        <small class="text-muted d-block text-uppercase" style="font-size: 10px;">SKU</small>
                        <span class="fw-bold"><?= htmlspecialchars($product['sku']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section-card p-4 mb-4 bg-white border shadow-sm">
                <?php if ($isOutOfStock): ?>
                    <div class="alert alert-warning rounded-4 mb-3">
                        Este producto estÃ¡ agotado por el momento.
                    </div>
                    <div class="text-center small text-muted">
                        Vuelve pronto para revisar su disponibilidad.
                    </div>
                <?php else: ?>
                    <form action="<?= site_url('carrito/agregar/' . (int) ($product['id'] ?? 0)); ?>" method="post">
                        <?= csrf_input(); ?>
                        <div class="qty-selector mb-3">
                            <label for="quantity" class="fw-bold small mb-2">CANTIDAD</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control rounded-4" style="max-width: 120px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold mb-3">Agregar al carrito</button>
                        <div class="text-center small text-muted">
                            <i class="fas fa-shield-heart text-success me-1"></i>
                            Pago seguro y envÃ­o con seguimiento.
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-7">
            <div class="section-card p-4 h-100 border">
                <ul class="nav info-tabs mb-4 border-bottom" role="tablist">
                    <li class="nav-item"><button class="nav-link active border-0 fw-bold px-4" data-bs-toggle="tab" data-bs-target="#descripcion" type="button">DescripciÃ³n</button></li>
                    <li class="nav-item"><button class="nav-link border-0 fw-bold px-4" data-bs-toggle="tab" data-bs-target="#resenas" type="button">ReseÃ±as</button></li>
                </ul>

                <div class="tab-content pt-2">
                    <div class="tab-pane fade show active" id="descripcion">
                        <p class="mb-4 lh-lg"><?= nl2br(htmlspecialchars($product['description'] ?? 'Sin descripciÃ³n disponible.')); ?></p>
                    </div>
                    <div class="tab-pane fade" id="resenas">
                        <?php if (!empty($reviews)): ?>
                            <div class="vstack gap-3">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                            <div>
                                                <strong><?= htmlspecialchars(trim(($review['nombre'] ?? '') . ' ' . ($review['apellidos'] ?? ''))); ?></strong>
                                                <div class="text-warning small"><?= str_repeat('â˜…', (int) ($review['calificacion'] ?? 0)); ?></div>
                                            </div>
                                            <small class="text-muted"><?= htmlspecialchars($review['created_at'] ?? ''); ?></small>
                                        </div>
                                        <?php if (!empty($review['titulo'])): ?>
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($review['titulo']); ?></h6>
                                        <?php endif; ?>
                                        <p class="text-muted mb-0 small"><?= nl2br(htmlspecialchars($review['comentario'] ?? '')); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0 text-center py-4">AÃºn no hay reseÃ±as para este producto.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="section-card p-4 h-100 border bg-light">
                <h3 class="h4 fw-bold mb-3">Escribe una reseÃ±a</h3>
                <?php if (empty($currentUser)): ?>
                    <p class="text-muted mb-3 small">Inicia sesiÃ³n para compartir tu experiencia.</p>
                    <a href="<?= site_url('auth/login'); ?>" class="btn btn-outline-primary w-100 rounded-pill">Iniciar sesiÃ³n</a>
                <?php elseif (!empty($hasReviewed)): ?>
                    <div class="alert alert-info rounded-4 mb-0">Ya reseÃ±aste este producto.</div>
                <?php elseif (empty($canReview)): ?>
                    <div class="alert alert-warning rounded-4 mb-0 small">PodrÃ¡s reseÃ±arlo cuando tengas un pedido entregado de este producto.</div>
                <?php else: ?>
                    <form method="post" action="<?= site_url('producto/' . ($product['slug'] ?? '') . '/review'); ?>" class="vstack gap-3">
                        <?= csrf_input(); ?>
                        <div>
                            <label for="rating" class="form-label small fw-bold">CalificaciÃ³n</label>
                            <select id="rating" name="rating" class="form-select rounded-4" required>
                                <option value="">Selecciona una calificaciÃ³n</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i; ?>"><?= $i; ?> estrella<?= $i > 1 ? 's' : ''; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="title" class="form-label small fw-bold">TÃ­tulo</label>
                            <input type="text" id="title" name="title" class="form-control rounded-4" maxlength="150" placeholder="Ej: Excelente producto">
                        </div>
                        <div>
                            <label for="comment" class="form-label small fw-bold">Comentario</label>
                            <textarea id="comment" name="comment" class="form-control rounded-4" rows="4" placeholder="CuÃ©ntanos cÃ³mo te fue..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2">Publicar reseÃ±a</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
