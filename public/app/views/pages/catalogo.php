<?php
$title = 'Catálogo de Suplementos | Biomedcs Souls - Sensea';
?>

<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-5">
        <div>
            <h1 class="display-5 fw-bold mb-2">Catálogo de Suplementos</h1>
            <p class="text-muted lead">Fórmulas premium Sensea para rendimiento mental y bienestar físico.</p>
        </div>
        <a href="<?= site_url('carrito'); ?>" class="btn btn-outline-primary btn-lg px-4">
            <i class="bi bi-cart"></i> Ver Carrito
        </a>
    </div>

    <div class="bg-white shadow-sm border rounded-4 p-4 mb-5">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input type="search" id="searchInput"
                           class="form-control border-0 shadow-none py-3"
                           placeholder="Buscar por nombre, ingrediente o beneficio...">
                </div>
            </div>

            <div class="col-lg-3">
                <select id="categoryFilter" class="form-select py-3 border-0 shadow-sm rounded-4">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['nombre']); ?>"
                                <?= (isset($currentCategory) && $currentCategory['id'] === $cat['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-3">
                <select id="sortFilter" class="form-select py-3 border-0 shadow-sm rounded-4">
                    <option value="recent">Más recientes</option>
                    <option value="price_asc">Precio: Menor a Mayor</option>
                    <option value="price_desc">Precio: Mayor a Menor</option>
                    <option value="name_asc">Nombre A-Z</option>
                    <option value="name_desc">Nombre Z-A</option>
                </select>
            </div>

            <div class="col-lg-1 text-end">
                <button id="clearFilters" class="btn btn-outline-secondary w-100 py-3 rounded-4">Limpiar</button>
            </div>
        </div>
    </div>

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
                <div class="col product-item"
                     data-name="<?= strtolower(htmlspecialchars($product['nombre'])); ?>"
                     data-category="<?= strtolower(htmlspecialchars($product['categoria_nombre'] ?? '')); ?>"
                     data-price="<?= $displayPrice; ?>"
                     data-order="<?= htmlspecialchars($product['updated_at'] ?? ''); ?>">
                    <div class="product-card h-100 border-0 shadow-sm overflow-hidden rounded-4">
                        <?php if (!empty($product['imagen_principal'])): ?>
                            <img src="<?= asset_url('img/products/' . $product['imagen_principal']); ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($product['nombre']); ?>"
                                 style="height:240px; object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="height:240px;">
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
                                    <a href="<?= site_url('producto/' . $product['slug']); ?>"
                                       class="btn btn-outline-primary btn-sm">Detalles</a>
                                    <?php if ($isOutOfStock): ?>
                                        <span class="small fw-semibold text-danger">Agotado</span>
                                    <?php else: ?>
                                        <form action="<?= site_url('carrito/agregar/' . $product['id']); ?>"
                                              method="post" class="d-inline">
                                            <?= csrf_input(); ?>
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
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

        <p id="noResults" class="text-center text-muted py-5 d-none">
            No se encontraron productos con los filtros seleccionados.
        </p>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h5>No hay productos disponibles en este momento.</h5>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput    = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortFilter     = document.getElementById('sortFilter');
        const clearBtn       = document.getElementById('clearFilters');
        const grid           = document.getElementById('productsGrid');
        const noResults      = document.getElementById('noResults');

        if (!grid) return;

        function getItems() {
            return Array.from(grid.querySelectorAll('.product-item'));
        }

        function filterAndSort() {
            const searchTerm       = searchInput.value.toLowerCase().trim();
            const selectedCategory = categoryFilter.value.toLowerCase();
            const sortValue        = sortFilter.value;

            let items = getItems();

            // Filter
            items.forEach(item => {
                const name     = item.dataset.name     || '';
                const category = item.dataset.category || '';
                const matchSearch   = !searchTerm       || name.includes(searchTerm);
                const matchCategory = !selectedCategory || category === selectedCategory;
                item.style.display = (matchSearch && matchCategory) ? '' : 'none';
            });

            // Sort visible items
            const visible = items.filter(item => item.style.display !== 'none');

            visible.sort((a, b) => {
                switch (sortValue) {
                    case 'price_asc':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price_desc':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'name_asc':
                        return (a.dataset.name || '').localeCompare(b.dataset.name || '', 'es');
                    case 'name_desc':
                        return (b.dataset.name || '').localeCompare(a.dataset.name || '', 'es');
                    default: // 'recent' — keep original DOM order
                        return 0;
                }
            });

            // Re-append in sorted order (hidden ones stay at the end naturally)
            visible.forEach(item => grid.appendChild(item));

            // Show/hide the "no results" message
            if (noResults) {
                noResults.classList.toggle('d-none', visible.length > 0);
            }
        }

        searchInput.addEventListener('input',  filterAndSort);
        categoryFilter.addEventListener('change', filterAndSort);
        sortFilter.addEventListener('change',  filterAndSort);

        clearBtn.addEventListener('click', function () {
            searchInput.value    = '';
            categoryFilter.value = '';
            sortFilter.value     = 'recent';
            filterAndSort();
        });
    });
</script>
