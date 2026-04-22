<?php
$title = $title ?? ($product['name'] ?? 'Producto');
$galleryImages = array_values(array_filter($images ?? [], static fn ($image) => !empty($image['url_imagen'])));
$mainImage = !empty($galleryImages)
    ? asset_url('img/products/' . $galleryImages[0]['url_imagen'])
    : (!empty($product['image']) ? $product['image'] : null);
$displayPrice = (float) ($product['price'] ?? 0);
?>

<div class="container py-5">
    <nav class="breadcrumb-custom">
        <a href="<?= site_url(); ?>">Inicio</a>
        <span class="mx-2">/</span>
        <a href="<?= site_url('catalogo'); ?>">Catálogo</a>
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
            <div class="section-card p-4">
                <div class="rounded-4 overflow-hidden bg-white border mb-3">
                    <?php if ($mainImage): ?>
                        <img src="<?= htmlspecialchars($mainImage); ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Producto'); ?>" class="img-fluid w-100" style="max-height: 520px; object-fit: cover;">
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
        </div>

        <div class="col-lg-6">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($badges ?? [] as $badge): ?>
                    <span class="badge rounded-pill px-3 py-2" style="background: <?= htmlspecialchars($badge['color'] ?? '#3B82F6'); ?>20; color: <?= htmlspecialchars($badge['color'] ?? '#3B82F6'); ?>;">
                        <?= htmlspecialchars($badge['nombre'] ?? 'Etiqueta'); ?>
                    </span>
                <?php endforeach; ?>
                <?php if (!empty($product['category'])): ?>
                    <span class="badge rounded-pill text-bg-light px-3 py-2"><?= htmlspecialchars($product['category']); ?></span>
                <?php endif; ?>
            </div>

            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($product['name'] ?? 'Producto'); ?></h1>

            <div class="d-flex flex-wrap align-items-center gap-4 mb-4">
                <div class="price-big mb-0">$<?= number_format($displayPrice, 2); ?></div>
                <div class="text-warning fw-semibold">
                    <?= str_repeat('★', (int) round((float) ($product['rating'] ?? 0))); ?>
                    <span class="text-muted ms-2"><?= number_format((float) ($product['rating'] ?? 0), 1); ?> (<?= (int) ($product['reviews_count'] ?? 0); ?> reseñas)</span>
                </div>
            </div>

            <?php if (!empty($product['short_description'])): ?>
                <p class="lead text-muted mb-4"><?= htmlspecialchars($product['short_description']); ?></p>
            <?php endif; ?>

            <div class="d-flex flex-wrap gap-3 mb-4">
                <?php if (!empty($product['content'])): ?>
                    <div class="product-badge-info">
                        <span class="label">Contenido</span>
                        <span class="value"><?= htmlspecialchars($product['content']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($product['sku'])): ?>
                    <div class="product-badge-info">
                        <span class="label">SKU</span>
                        <span class="value"><?= htmlspecialchars($product['sku']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($product['benefits'])): ?>
                <ul class="benefits-list">
                    <?php foreach ($product['benefits'] as $benefit): ?>
                        <li><i class="fas fa-circle-check"></i><?= htmlspecialchars($benefit); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="<?= site_url('carrito/agregar/' . (int) ($product['id'] ?? 0)); ?>" method="post" class="section-card p-4 mb-4">
                <?= csrf_input(); ?>
                <div class="qty-selector">
                    <label for="quantity" class="fw-semibold mb-0">Cantidad</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control rounded-4" style="max-width: 120px;">
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-5">Agregar al carrito</button>
                <div class="guarantee-badge">
                    <i class="fas fa-shield-heart text-success"></i>
                    <span>Pago seguro y envío con seguimiento.</span>
                </div>
            </form>

            <div class="usage-box">
                <h4>Modo de empleo</h4>
                <p class="mb-0"><?= nl2br(htmlspecialchars($product['usage_instructions'] ?? 'Consulta a tu especialista antes de consumir este suplemento.')); ?></p>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-7">
            <div class="section-card p-4 h-100">
                <ul class="nav info-tabs mb-4" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#descripcion" type="button">Descripción</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#resenas" type="button">Reseñas</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="descripcion">
                        <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'] ?? 'Sin descripción disponible.')); ?></p>
                    </div>
                    <div class="tab-pane fade" id="resenas">
                        <?php if (!empty($reviews)): ?>
                            <div class="vstack gap-3">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border rounded-4 p-3">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                            <div>
                                                <strong><?= htmlspecialchars(trim(($review['nombre'] ?? '') . ' ' . ($review['apellidos'] ?? ''))); ?></strong>
                                                <div class="text-warning small"><?= str_repeat('★', (int) ($review['calificacion'] ?? 0)); ?></div>
                                            </div>
                                            <small class="text-muted"><?= htmlspecialchars($review['created_at'] ?? ''); ?></small>
                                        </div>
                                        <?php if (!empty($review['titulo'])): ?>
                                            <h6 class="mb-1"><?= htmlspecialchars($review['titulo']); ?></h6>
                                        <?php endif; ?>
                                        <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($review['comentario'] ?? '')); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Aún no hay reseñas para este producto.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="section-card p-4 h-100">
                <h3 class="h4 fw-bold mb-3">Escribe una reseña</h3>

                <?php if (empty($currentUser)): ?>
                    <p class="text-muted mb-3">Inicia sesión para compartir tu experiencia.</p>
                    <a href="<?= site_url('auth/login'); ?>" class="btn btn-outline-primary">Iniciar sesión</a>
                <?php elseif (!empty($hasReviewed)): ?>
                    <div class="alert alert-info rounded-4 mb-0">Ya reseñaste este producto.</div>
                <?php elseif (empty($canReview)): ?>
                    <div class="alert alert-warning rounded-4 mb-0">Podrás reseñarlo cuando tengas un pedido entregado de este producto.</div>
                <?php else: ?>
                    <form method="post" action="<?= site_url('producto/' . ($product['slug'] ?? '') . '/review'); ?>" class="vstack gap-3">
                        <?= csrf_input(); ?>
                        <div>
                            <label for="rating" class="form-label fw-semibold">Calificación</label>
                            <select id="rating" name="rating" class="form-select rounded-4" required>
                                <option value="">Selecciona una calificación</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i; ?>"><?= $i; ?> estrella<?= $i > 1 ? 's' : ''; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="title" class="form-label fw-semibold">Título</label>
                            <input type="text" id="title" name="title" class="form-control rounded-4" maxlength="150" placeholder="Resume tu experiencia">
                        </div>
                        <div>
                            <label for="comment" class="form-label fw-semibold">Comentario</label>
                            <textarea id="comment" name="comment" class="form-control rounded-4" rows="5" placeholder="Cuéntanos cómo te fue con este producto"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Publicar reseña</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
