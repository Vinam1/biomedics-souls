<?php
$title = $title ?? ($product['name'] ?? 'Producto');
$galleryImages = array_values(array_filter($images ?? [], static fn ($image) => !empty($image['url_imagen'])));
$mainImage = !empty($galleryImages)
        ? asset_url('img/products/' . $galleryImages[0]['url_imagen'])
        : (!empty($product['image']) ? $product['image'] : null);
$displayPrice = (float) ($product['price'] ?? 0);
$statusLabel = trim((string) ($product['status'] ?? ''));
$isOutOfStock = !empty($product['is_out_of_stock']);
$rating = (int) round((float) ($product['rating'] ?? 0));
$ratingValue = (float) ($product['rating'] ?? 0);
$reviewsCount = (int) ($product['reviews_count'] ?? 0);
$presentationValue = trim((string) ($product['presentation'] ?? $product['form_name'] ?? $product['form'] ?? 'Cápsulas vegetales'));
$contentValue = trim((string) ($product['content'] ?? '60 cápsulas'));
$categoryValue = trim((string) ($product['category'] ?? 'Herbales'));
?>

<div class="container py-5 product-page">
    <nav class="breadcrumb-custom mb-4">
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
            <div class="product-gallery-card">
                <div class="product-gallery-frame">
                    <?php if ($mainImage): ?>
                        <img id="mainImage" src="<?= htmlspecialchars($mainImage); ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Producto'); ?>" class="img-fluid w-100 product-main-image">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center text-muted product-main-image-placeholder">Sin imagen disponible</div>
                    <?php endif; ?>
                </div>

                <?php if (count($galleryImages) > 1): ?>
                    <div class="product-thumb-grid">
                        <?php foreach ($galleryImages as $image): ?>
                            <button type="button" class="product-thumb-button border-0 bg-transparent p-0" data-image="<?= htmlspecialchars(asset_url('img/products/' . $image['url_imagen'])); ?>">
                                <img src="<?= htmlspecialchars(asset_url('img/products/' . $image['url_imagen'])); ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Producto'); ?>" class="product-thumbnail">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
            </div>

            <div class="product-copy-panel">
                <h1 class="product-page-title"><?= htmlspecialchars($product['name'] ?? 'Producto'); ?></h1>

                <div class="product-rating-row">
                    <div class="d-inline-flex gap-1 text-warning">
                        <?php for ($i = 0; $i < $rating; $i++): ?>
                            <i class="bi bi-star-fill"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted"><?= number_format($ratingValue, 1); ?> (<?= $reviewsCount; ?> reseñas)</span>
                </div>

                <div class="product-price-row">
                    <div class="price-big mb-0 fw-bold">$<?= number_format($displayPrice, 2); ?></div>
                    <span class="product-price-note">Envío nacional y soporte personalizado</span>
                </div>

                <?php if (!empty($product['short_description'])): ?>
                    <p class="product-intro"><?= htmlspecialchars($product['short_description']); ?></p>
                <?php endif; ?>

                <div class="product-feature-grid">
                    <article class="product-feature-card">
                        <div class="product-feature-icon"><i class="bi bi-box-seam"></i></div>
                        <span class="product-feature-label">Presentación</span>
                        <strong class="product-feature-value"><?= htmlspecialchars($presentationValue); ?></strong>
                    </article>
                    <article class="product-feature-card">
                        <div class="product-feature-icon"><i class="bi bi-capsule-pill"></i></div>
                        <span class="product-feature-label">Contenido</span>
                        <strong class="product-feature-value"><?= htmlspecialchars($contentValue); ?></strong>
                    </article>
                    <article class="product-feature-card">
                        <div class="product-feature-icon"><i class="bi bi-bookmark"></i></div>
                        <span class="product-feature-label">Categoría</span>
                        <strong class="product-feature-value"><?= htmlspecialchars($categoryValue); ?></strong>
                    </article>
                </div>

                <div class="product-purchase-card">
                    <?php if ($isOutOfStock): ?>
                        <div class="alert alert-warning rounded-4 mb-3">
                            Este producto está agotado por el momento.
                        </div>
                        <div class="text-center small text-muted">
                            Vuelve pronto para revisar su disponibilidad.
                        </div>
                    <?php else: ?>
                        <form action="<?= site_url('carrito/agregar/' . (int) ($product['id'] ?? 0)); ?>" method="post">
                            <?= csrf_input(); ?>
                            <div class="qty-selector product-qty-row mb-3">
                                <label for="quantity" class="fw-bold small mb-2">Cantidad</label>
                                <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control rounded-4 product-qty-input">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold mb-3">Agregar al carrito</button>
                            <div class="text-center small text-muted">
                                <i class="fas fa-shield-heart text-success me-1"></i>
                                Pago seguro y envío con seguimiento.
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-7">
            <div class="section-card p-4 h-100 border">
                <ul class="nav info-tabs mb-4 border-bottom" role="tablist">
                    <li class="nav-item"><button class="nav-link active border-0 fw-bold px-4" data-bs-toggle="tab" data-bs-target="#descripcion" type="button">Descripción</button></li>
                    <li class="nav-item"><button class="nav-link border-0 fw-bold px-4" data-bs-toggle="tab" data-bs-target="#resenas" type="button">Reseñas</button></li>
                </ul>

                <div class="tab-content pt-2">
                    <div class="tab-pane fade show active" id="descripcion">
                        <p class="mb-4 lh-lg"><?= nl2br(htmlspecialchars($product['description'] ?? 'Sin descripción disponible.')); ?></p>
                    </div>
                    <div class="tab-pane fade" id="resenas">
                        <?php if (!empty($reviews)): ?>
                            <div class="vstack gap-3">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                            <div>
                                                <strong><?= htmlspecialchars(trim(($review['nombre'] ?? '') . ' ' . ($review['apellidos'] ?? ''))); ?></strong>
                                                <div class="text-warning small">
                                                    <?php for ($i = 0; $i < (int) ($review['calificacion'] ?? 0); $i++): ?>
                                                        <i class="bi bi-star-fill"></i>
                                                    <?php endfor; ?>
                                                </div>
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
                            <p class="text-muted mb-0 text-center py-4">Aún no hay reseñas para este producto.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="section-card p-4 h-100 border bg-light">
                <?php if (!empty($product['benefits'])): ?>
                    <div class="mb-4">
                        <h3 class="h5 fw-bold mb-3">Beneficios destacados</h3>
                        <ul class="benefits-list list-unstyled mb-0">
                            <?php foreach ($product['benefits'] as $benefit): ?>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-circle-check text-success me-2 mt-1"></i>
                                    <span class="text-muted small"><?= htmlspecialchars($benefit); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="usage-box product-usage-box mb-4">
                    <h4>Modo de empleo</h4>
                    <p class="mb-0 small text-muted">
                        <?= nl2br(htmlspecialchars($product['usage_instructions'] ?? 'Consulta a tu especialista antes de consumir este suplemento.')); ?>
                    </p>
                </div>

                <h3 class="h4 fw-bold mb-3">Escribe una reseña</h3>
                <?php if (empty($currentUser)): ?>
                    <p class="text-muted mb-3 small">Inicia sesión para compartir tu experiencia.</p>
                    <a href="<?= site_url('auth/login'); ?>" class="btn btn-outline-primary w-100 rounded-pill">Iniciar sesión</a>
                <?php elseif (!empty($hasReviewed)): ?>
                    <div class="alert alert-info rounded-4 mb-0">Ya reseñaste este producto.</div>
                <?php elseif (empty($canReview)): ?>
                    <div class="alert alert-warning rounded-4 mb-0 small">Podrás reseñarlo cuando tengas un pedido entregado de este producto.</div>
                <?php else: ?>
                    <form method="post" action="<?= site_url('producto/' . ($product['slug'] ?? '') . '/review'); ?>" class="vstack gap-3">
                        <?= csrf_input(); ?>
                        <div>
                            <label for="rating" class="form-label small fw-bold">Calificación</label>
                            <select id="rating" name="rating" class="form-select rounded-4" required>
                                <option value="">Selecciona una calificación</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i; ?>"><?= $i; ?> estrella<?= $i > 1 ? 's' : ''; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="title" class="form-label small fw-bold">Título</label>
                            <input type="text" id="title" name="title" class="form-control rounded-4" maxlength="150" placeholder="Ej: Excelente producto">
                        </div>
                        <div>
                            <label for="comment" class="form-label small fw-bold">Comentario</label>
                            <textarea id="comment" name="comment" class="form-control rounded-4" rows="4" placeholder="Cuéntanos cómo te fue..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2">Publicar reseña</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (count($galleryImages) > 1): ?>
<script>
    document.querySelectorAll('.product-thumb-button').forEach((button) => {
        button.addEventListener('click', () => {
            const mainImage = document.getElementById('mainImage');
            if (!mainImage) return;
            mainImage.src = button.dataset.image;
            document.querySelectorAll('.product-thumb-button').forEach((item) => item.classList.remove('is-active'));
            button.classList.add('is-active');
        });
    });

    const firstThumb = document.querySelector('.product-thumb-button');
    if (firstThumb) {
        firstThumb.classList.add('is-active');
    }
</script>
<?php endif; ?>
