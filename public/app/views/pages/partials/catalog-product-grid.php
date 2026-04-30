<?php if (!empty($products)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="productsGrid">
        <?php foreach ($products as $product): ?>
            <?php
            $displayPrice = !empty($product['precio_descuento'])
                    ? (float) $product['precio_descuento']
                    : (float) $product['precio'];
            $statusLabel = trim((string) ($product['estatus'] ?? ''));
            $isOutOfStock = Producto::isOutOfStockStatus($statusLabel);
            ?>
            <div class="col product-item">
                <div class="product-card h-100 border-0 shadow-sm overflow-hidden rounded-4">
                    <?php if (!empty($product['imagen_principal'])): ?>
                        <img src="<?= asset_url('img/products/' . $product['imagen_principal']); ?>"
                             class="card-img-top"
                             alt="<?= htmlspecialchars($product['nombre']); ?>"
                             style="height:240px; object-fit:cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:240px;">
                            <span class="text-muted">Sin imagen</span>
                        </div>
                    <?php endif; ?>

                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <?= htmlspecialchars($product['categoria_nombre'] ?? 'General'); ?>
                                </span>
                                <?php if ($statusLabel !== ''): ?>
                                    <span class="badge rounded-pill <?= $isOutOfStock ? 'text-bg-danger' : 'bg-light text-dark border'; ?>">
                                        <?= htmlspecialchars($statusLabel); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($product['precio_descuento'])): ?>
                                <span class="badge bg-success">Oferta</span>
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-semibold mb-2"><?= htmlspecialchars($product['nombre']); ?></h5>
                        <p class="text-muted small mb-4">
                            <?= htmlspecialchars($product['descripcion_corta'] ?? 'Suplemento premium Sensea'); ?>
                        </p>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <?php if (!empty($product['precio_descuento'])): ?>
                                    <span class="text-muted text-decoration-line-through small">
                                        $<?= number_format($product['precio'], 2); ?>
                                    </span><br>
                                    <span class="h5 fw-bold text-success">
                                        $<?= number_format($product['precio_descuento'], 2); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="h5 fw-bold">$<?= number_format($product['precio'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="<?= site_url('producto/' . $product['slug']); ?>" class="btn btn-outline-primary btn-sm">Detalles</a>
                                <?php if ($isOutOfStock): ?>
                                    <span class="small fw-semibold text-danger">Agotado</span>
                                <?php else: ?>
                                    <form action="<?= site_url('carrito/agregar/' . $product['id']); ?>" method="post" class="d-inline">
                                        <?= csrf_input(); ?>
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary btn-sm" aria-label="Agregar al carrito">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p id="noResults" class="text-center text-muted py-5 mb-0">
        No se encontraron productos con los filtros seleccionados.
    </p>
<?php endif; ?>
