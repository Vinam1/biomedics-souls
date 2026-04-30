<?php
$title              = 'Catálogo de Suplementos | Biomedics Souls - Sensea';
$filters            = $filters ?? [];
$searchValue        = (string) ($filters['query'] ?? '');
$selectedCategoryId = (int) ($filters['categoria_id'] ?? ($currentCategory['id'] ?? 0));

// Compatibilidad PHP 7.4 - Reemplazo de 'match'
$sortInput = (string) ($filters['sort'] ?? 'updated_at_desc');

$sortMap = [
    'precio_asc'      => 'price_asc',
    'precio_desc'     => 'price_desc',
    'nombre_asc'      => 'name_asc',
    'nombre_desc'     => 'name_desc',
    'updated_at_desc' => 'recent',
];

$selectedSort = isset($sortMap[$sortInput]) ? $sortMap[$sortInput] : 'recent';

$ajaxEndpoint = site_url('catalogo');
?>

<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-5">
        <div>
            <h1 class="display-5 fw-bold mb-2">Catálogo de Suplementos</h1>
            <p class="text-muted lead">Fórmulas premium Sensea para rendimiento mental y bienestar físico.</p>
        </div>
        <a href="<?= site_url('carrito'); ?>" class="btn btn-outline-primary btn-lg px-4">
            <i class="bi bi-cart"></i> Ver carrito
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-sm border rounded-4 p-4 mb-5">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input type="search"
                           id="searchInput"
                           class="form-control border-0 shadow-none py-3"
                           value="<?= htmlspecialchars($searchValue); ?>"
                           placeholder="Buscar por nombre, ingrediente o beneficio...">
                </div>
            </div>

            <div class="col-lg-3">
                <select id="categoryFilter" class="form-select py-3 border-0 shadow-sm rounded-4">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= (int) $cat['id']; ?>"
                                <?= $selectedCategoryId === (int) $cat['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-3">
                <select id="sortFilter" class="form-select py-3 border-0 shadow-sm rounded-4">
                    <option value="recent"     <?= $selectedSort === 'recent'     ? 'selected' : ''; ?>>Más recientes</option>
                    <option value="price_asc"  <?= $selectedSort === 'price_asc'  ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                    <option value="price_desc" <?= $selectedSort === 'price_desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                    <option value="name_asc"   <?= $selectedSort === 'name_asc'   ? 'selected' : ''; ?>>Nombre A-Z</option>
                    <option value="name_desc"  <?= $selectedSort === 'name_desc'  ? 'selected' : ''; ?>>Nombre Z-A</option>
                </select>
            </div>

            <div class="col-lg-1 text-end">
                <button id="clearFilters" class="btn btn-outline-secondary w-100 py-3 rounded-4">Limpiar</button>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div id="catalogResults" data-endpoint="<?= htmlspecialchars($ajaxEndpoint); ?>">
        <?php require APPROOT . '/views/pages/partials/catalog-product-grid.php'; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput    = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter     = document.getElementById('sortFilter');
    const clearBtn       = document.getElementById('clearFilters');
    const results        = document.getElementById('catalogResults');

    if (!results) return;

    const rawEndpoint = results.dataset.endpoint;
    const endpointUrl = new URL(rawEndpoint, window.location.origin);

    let debounceTimer = null;

    function buildFetchUrl() {
        const url = new URL(endpointUrl.toString());
        url.searchParams.set('ajax', '1');

        const query    = searchInput.value.trim();
        const category = categoryFilter.value;
        const sort     = sortFilter.value;

        if (query)    url.searchParams.set('q', query);
        if (category) url.searchParams.set('categoria', category);
        if (sort && sort !== 'recent') url.searchParams.set('sort', sort);

        return url.toString();
    }

    async function fetchCatalog() {
        const fetchUrl = buildFetchUrl();
        try {
            const response = await fetch(fetchUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const payload = await response.json();
            results.innerHTML = payload.html ?? '';
        } catch (err) {
            console.error('Error cargando catálogo:', err);
            results.innerHTML = '<p class="text-center text-danger py-5 mb-0">No se pudieron cargar los productos. Intenta nuevamente.</p>';
        }
    }

    function queueFetch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchCatalog, 300);
    }

    // Eventos
    searchInput.addEventListener('input', queueFetch);
    categoryFilter.addEventListener('change', queueFetch);
    sortFilter.addEventListener('change', queueFetch);

    clearBtn.addEventListener('click', function () {
        searchInput.value    = '';
        categoryFilter.value = '';
        sortFilter.value     = 'recent';
        queueFetch();
    });
});
</script>