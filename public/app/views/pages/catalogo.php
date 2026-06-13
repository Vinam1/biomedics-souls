<?php
$title              = 'Catálogo de Suplementos | Biomedics Souls - Sensea';
$filters            = $filters ?? [];
$searchValue        = (string) ($filters['query'] ?? '');
$selectedCategoryId = (int) ($filters['categoria_id'] ?? ($currentCategory['id'] ?? 0));
$recommendedIds     = array_values(array_filter(array_map('intval', $filters['product_ids'] ?? [])));
$recommendedParam   = implode(',', $recommendedIds);

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

<section class="page-shell page-shell-catalog py-5">
    <div class="container">
        <div class="page-hero page-hero-compact mb-5" data-animate="fade-up">
            <div class="row align-items-end g-4">
                <div class="col-lg-8">
                    <span class="eyebrow-pill">Catálogo premium</span>
                    <h1 class="page-title mb-3">Fórmulas diseñadas para <span class="text-gradient">bienestar real</span></h1>
                    <p class="page-subtitle mb-0">Explora suplementos con enfoque científico, ingredientes premium y una experiencia visual más clara para elegir con confianza.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= site_url('carrito'); ?>" class="btn btn-soft btn-lg px-4">
                        <i class="bi bi-cart me-2"></i> Ver carrito
                    </a>
                </div>
            </div>
        </div>

        <div class="section-card filter-panel p-4 mb-5" data-animate="fade-up" data-animate-delay="80">
            <?php if (!empty($recommendedIds)): ?>
                <div id="recommendedNotice" class="alert alert-primary border-0 rounded-4 mb-4">
                    <i class="bi bi-stars me-2"></i>
                    Mostrando productos recomendados seg&uacute;n tu quiz.
                </div>
            <?php endif; ?>
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <label for="searchInput" class="form-label small fw-bold text-uppercase text-muted mb-2">Buscar</label>
                    <div class="search-shell search-shell-panel w-100">
                        <i class="bi bi-search"></i>
                        <input
                            type="search"
                            id="searchInput"
                            class="form-control border-0 shadow-none"
                            value="<?= htmlspecialchars($searchValue); ?>"
                            placeholder="Nombre, ingrediente o beneficio..."
                        >
                    </div>
                </div>

                <div class="col-lg-3">
                    <label for="categoryFilter" class="form-label small fw-bold text-uppercase text-muted mb-2">Categoría</label>
                    <select id="categoryFilter" class="form-select filter-select">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories ?? [] as $cat): ?>
                            <option value="<?= (int) $cat['id']; ?>" <?= $selectedCategoryId === (int) $cat['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-3">
                    <label for="sortFilter" class="form-label small fw-bold text-uppercase text-muted mb-2">Ordenar</label>
                    <select id="sortFilter" class="form-select filter-select">
                        <option value="recent" <?= $selectedSort === 'recent' ? 'selected' : ''; ?>>Más recientes</option>
                        <option value="price_asc" <?= $selectedSort === 'price_asc' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                        <option value="price_desc" <?= $selectedSort === 'price_desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                        <option value="name_asc" <?= $selectedSort === 'name_asc' ? 'selected' : ''; ?>>Nombre A-Z</option>
                        <option value="name_desc" <?= $selectedSort === 'name_desc' ? 'selected' : ''; ?>>Nombre Z-A</option>
                    </select>
                </div>

                <div class="col-lg-1">
                    <label class="form-label d-none d-lg-block mb-2">&nbsp;</label>
                    <button id="clearFilters" class="btn btn-outline-secondary w-100 filter-clear-btn">Limpiar</button>
                </div>
            </div>
        </div>

        <div
            id="catalogResults"
            data-endpoint="<?= htmlspecialchars($ajaxEndpoint); ?>"
            data-recommended="<?= htmlspecialchars($recommendedParam); ?>"
            data-animate="fade-up"
            data-animate-delay="140"
        >
            <?php require APPROOT . '/views/pages/partials/catalog-product-grid.php'; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const clearBtn = document.getElementById('clearFilters');
    const results = document.getElementById('catalogResults');
    const recommendedNotice = document.getElementById('recommendedNotice');

    if (!results) return;

    const rawEndpoint = results.dataset.endpoint;
    let recommendedIds = results.dataset.recommended || '';
    const endpointUrl = new URL(rawEndpoint, window.location.origin);
    let debounceTimer = null;

    function buildFetchUrl() {
        const url = new URL(endpointUrl.toString());
        url.searchParams.set('ajax', '1');

        const query = searchInput.value.trim();
        const category = categoryFilter.value;
        const sort = sortFilter.value;

        if (query) url.searchParams.set('q', query);
        if (category) url.searchParams.set('categoria', category);
        if (recommendedIds) url.searchParams.set('recommended', recommendedIds);
        if (sort && sort !== 'recent') url.searchParams.set('sort', sort);

        return url.toString();
    }

    async function fetchCatalog() {
        const fetchUrl = buildFetchUrl();
        results.classList.add('catalog-loading');

        try {
            const response = await fetch(fetchUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const payload = await response.json();
            results.innerHTML = payload.html ?? '';

            if (window.initBiomedicsAnimations) {
                window.initBiomedicsAnimations(results);
            }
            if (window.initBiomedicsProductCards) {
                window.initBiomedicsProductCards(results);
            }
        } catch (err) {
            console.error('Error cargando catálogo:', err);
            results.innerHTML = '<p class="text-center text-danger py-5 mb-0">No se pudieron cargar los productos. Intenta nuevamente.</p>';
        } finally {
            results.classList.remove('catalog-loading');
        }
    }

    function queueFetch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchCatalog, 300);
    }

    searchInput.addEventListener('input', queueFetch);
    categoryFilter.addEventListener('change', queueFetch);
    sortFilter.addEventListener('change', queueFetch);

    clearBtn.addEventListener('click', function () {
        recommendedIds = '';
        if (recommendedNotice) recommendedNotice.classList.add('d-none');
        searchInput.value = '';
        categoryFilter.value = '';
        sortFilter.value = 'recent';
        queueFetch();
    });
});
</script>
