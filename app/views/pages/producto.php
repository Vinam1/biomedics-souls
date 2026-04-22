<div class="container py-5">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <nav class="breadcrumb-custom">
        <a href="<?= site_url('home'); ?>">Inicio</a> /
        <a href="<?= site_url('catalogo'); ?>">Catálogo</a> /
        <span><?= htmlspecialchars($product['name']); ?></span>
    </nav>

    <div class="row g-5">
        <div class="col-lg-5">
            <div class="position-relative">
                <?php if ($product['thumbnail']): ?>
                    <img src="<?= $product['thumbnail']; ?>" alt="Thumbnail" class="product-thumbnail position-absolute top-0 start-0 m-3 shadow-sm" style="z-index: 2;">
                <?php endif; ?>

                <div class="bg-white rounded-5 p-5 d-flex align-items-center justify-content-center shadow-sm" style="min-height: 500px;">
                    <?php if ($product['image']): ?>
                        <img src="<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="img-fluid" style="max-height: 450px;">
                    <?php else: ?>
                        <div class="text-muted">Sin imagen del producto</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="fw-bold"><?= number_format($product['rating'], 1); ?></span>
                <div class="text-warning">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i <= round($product['rating']) ? '' : '-o'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-muted small">(<?= $product['reviews_count']; ?> reseñas)</span>
            </div>

            <h1 class="display-5 fw-bold mb-2"><?= htmlspecialchars($product['name']); ?></h1>
            <p class="lead text-muted mb-4"><?= htmlspecialchars($product['short_description']); ?></p>
            <div class="price-big">$<?= number_format($product['price'], 2); ?></div>

            <div class="d-flex flex-wrap gap-3 mb-4">
                <div class="product-badge-info"><span class="label">Presentación</span><span class="value"><?= htmlspecialchars($product['presentation'] ?: 'N/A'); ?></span></div>
                <div class="product-badge-info"><span class="label">Contenido</span><span class="value"><?= htmlspecialchars($product['content'] ?: 'N/A'); ?></span></div>
                <div class="product-badge-info"><span class="label">Categoría</span><span class="value"><?= htmlspecialchars($product['category']); ?></span></div>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-3">Beneficios Clave</h5>
                <ul class="benefits-list">
                    <?php if (!empty($product['benefits'])): ?>
                        <?php foreach ($product['benefits'] as $benefit): ?>
                            <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars($benefit); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><i class="fas fa-check-circle"></i> Calidad premium garantizada</li>
                    <?php endif; ?>
                </ul>
            </div>

            <form action="<?= site_url('carrito/agregar/' . $product['id']); ?>" method="post" class="qty-selector">
                <?= csrf_input(); ?>
                <div class="d-flex align-items-center">
                    <button type="button" class="qty-btn" onclick="updateQty(-1)"><i class="fas fa-minus"></i></button>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control text-center mx-2" style="width: 60px; border-radius: 10px;">
                    <button type="button" class="qty-btn" onclick="updateQty(1)"><i class="fas fa-plus"></i></button>
                </div>
                <button type="submit" id="add-to-cart-btn" class="btn btn-purple btn-lg flex-grow-1 py-3">Añadir al Carrito - $<?= number_format($product['price'], 2); ?></button>
            </form>

            <div class="d-flex flex-wrap gap-4 border-top pt-4 mt-2">
                <div class="guarantee-badge"><i class="fas fa-shield-alt text-purple"></i><span>Garantía de 30 días</span></div>
                <div class="guarantee-badge"><i class="fas fa-vial text-purple"></i><span>Testado por terceros</span></div>
            </div>
        </div>
    </div>

    <div class="mt-5 pt-5">
        <ul class="nav nav-tabs info-tabs mb-4" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Descripción</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="nutri-tab" data-bs-toggle="tab" data-bs-target="#nutri" type="button" role="tab">Info Nutricional</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reseñas (<?= count($reviews); ?>)</button></li>
        </ul>
        <div class="tab-content pb-5" id="productTabsContent">
            <div class="tab-pane fade show active" id="desc" role="tabpanel">
                <div class="row g-5">
                    <div class="col-lg-7">
                        <h3 class="fw-bold mb-4">Nuestra Filosofía de Calidad</h3>
                        <div class="text-muted" style="line-height: 1.8;"><?= nl2br(htmlspecialchars($product['description'] ?: 'En Biomedics Souls, nos dedicamos a ofrecer suplementos de la más alta pureza y eficacia. Cada ingrediente es cuidadosamente seleccionado y validado científicamente.')); ?></div>
                    </div>
                    <div class="col-lg-5">
                        <div class="usage-box">
                            <h4>Instrucciones de Uso</h4>
                            <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($product['usage_instructions'] ?: 'Seguir las indicaciones del envase o consultar con su médico.')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nutri" role="tabpanel">
                <div class="bg-surface p-4 rounded-4"><h5 class="mb-3">Composición</h5><p class="text-muted">Información detallada sobre la composición del producto pronto disponible.</p></div>
            </div>
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="row"><div class="col-lg-8">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($review['nombre'] . ' ' . $review['apellidos']); ?></h6>
                                        <div class="text-warning small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $review['calificacion'] ? '' : '-o'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($review['created_at'])); ?></small>
                                </div>
                                <p class="text-muted mb-0 small"><?= nl2br(htmlspecialchars($review['comentario'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No hay reseñas todavía.</p>
                    <?php endif; ?>
                </div></div>
            </div>
        </div>
    </div>
</div>

<script>
function updateQty(delta) {
    const input = document.getElementById('quantity');
    let val = parseInt(input.value || '1', 10) + delta;
    if (val < 1) val = 1;
    input.value = val;

    const basePrice = <?= json_encode((float) $product['price']); ?>;
    const btn = document.getElementById('add-to-cart-btn');
    const total = (basePrice * val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    btn.innerHTML = `Añadir al Carrito - $${total}`;
}
</script>
