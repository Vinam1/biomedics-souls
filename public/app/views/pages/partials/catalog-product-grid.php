<?php if (!empty($products)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="productsGrid">
        <?php foreach ($products as $index => $product): ?>
            <?php
            $displayPrice = !empty($product['precio_descuento']) ? (float) $product['precio_descuento'] : (float) $product['precio'];
            $statusLabel = trim((string) ($product['estatus'] ?? ''));
            $isOutOfStock = Producto::isOutOfStockStatus($statusLabel);
            ?>
            <div class="col product-item" data-animate="fade-up" data-animate-delay="<?= ($index % 4) * 80; ?>">
                <article class="product-card product-card-catalog product-card-clickable h-100 overflow-hidden" data-product-link="<?= site_url('producto/' . $product['slug']); ?>" tabindex="0" role="link" aria-label="Ver detalle de <?= htmlspecialchars($product['nombre']); ?>">
                    <a href="<?= site_url('producto/' . $product['slug']); ?>" class="product-media product-media-catalog d-block">
                        <?php if (!empty($product['imagen_principal'])): ?>
                            <img src="<?= asset_url('img/products/' . $product['imagen_principal']); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nombre']); ?>">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        <?php endif; ?>
                        <div class="product-floating-badges">
                            <span class="badge bg-white text-dark border"><?= htmlspecialchars($product['categoria_nombre'] ?? 'General'); ?></span>
                            <?php if ($statusLabel !== ''): ?>
                                <span class="badge rounded-pill <?= $isOutOfStock ? 'text-bg-danger' : 'bg-light text-dark border'; ?>">
                                    <?= htmlspecialchars($statusLabel); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>

                    <div class="product-content product-content-catalog d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <h3 class="mb-0"><?= htmlspecialchars($product['nombre']); ?></h3>
                            <?php if (!empty($product['precio_descuento'])): ?>
                                <span class="badge bg-success">Oferta</span>
                            <?php endif; ?>
                        </div>

                        <p class="product-card-description"><?= htmlspecialchars($product['descripcion_corta'] ?? 'Suplemento premium Sensea'); ?></p>

                        <div class="product-meta product-meta-catalog align-items-center mt-auto">
                            <div class="price-stack">
                                <?php if (!empty($product['precio_descuento'])): ?>
                                    <span class="text-muted text-decoration-line-through small d-block mb-1">$<?= number_format($product['precio'], 2); ?></span>
                                <?php endif; ?>
                                <strong>$<?= number_format($displayPrice, 2); ?></strong>
                            </div>

                            <div class="d-flex gap-2 align-items-center product-card-actions">
                                <?php if ($isOutOfStock): ?>
                                    <span class="small fw-semibold text-danger">Agotado</span>
                                <?php else: ?>
                                    <form action="<?= site_url('carrito/agregar/' . $product['id']); ?>" method="post" class="d-inline">
                                        <?= csrf_input(); ?>
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? site_url('catalogo')); ?>">
                                        <button type="submit" class="btn btn-cart btn-brand btn-sm" aria-label="Agregar al carrito">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state text-center py-5 section-card" data-animate="fade-up">
        <i class="bi bi-search display-5 text-muted mb-3 d-block"></i>
        <p id="noResults" class="text-muted py-2 mb-0">
            No se encontraron productos con los filtros seleccionados.
        </p>
    </div>
<?php endif; ?>
