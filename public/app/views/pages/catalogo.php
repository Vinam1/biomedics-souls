<?php
$title              = 'Catálogo de Suplementos | Biomedics Souls - Sensea';
$selectedCategoryId = (int) ($filters['categoria_id'] ?? ($currentCategory['id'] ?? 0));
$selectedSort       = match ($filters['sort'] ?? 'updated_at_desc') {
    'precio_asc'      => 'price_asc',
    'precio_desc'     => 'price_desc',
    'nombre_asc'      => 'name_asc',
    'nombre_desc'     => 'name_desc',
    default           => 'recent',
};
$searchValue = (string) ($filters['query'] ?? '');

/*
 * Construimos la URL base para el fetch AJAX.
 * site_url('catalogo') puede devolver algo como:
 *   /public/index.php?url=catalogo
 * Necesitamos esa URL completa para que el JS la use directamente.
 */
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
    <div id="catalogResults"
         data-endpoint="<?= htmlspecialchars($ajaxEndpoint); ?>">
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

    /*
     * endpoint viene de PHP, por ejemplo:
     *   /public/index.php?url=catalogo
     * Lo convertimos a URL absoluta para poder añadir searchParams sin
     * romper el parámetro "url" que ya trae.
     */
    const rawEndpoint = results.dataset.endpoint;
    const endpointUrl = new URL(rawEndpoint, window.location.origin);

    let activeRequest = null;
    let debounceTimer = null;

    function setLoadingState(isLoading) {
        results.classList.toggle('catalog-loading', isLoading);
    }

    function buildFetchUrl() {
        /*
         * Clonamos la URL base para no mutar el objeto original en cada
         * llamada. Así el parámetro "url=catalogo" siempre está presente.
         */
        const url = new URL(endpointUrl.toString());

        // Señal que indica al controlador que debe devolver JSON
        url.searchParams.set('ajax', '1');

        const query    = searchInput.value.trim();
        const category = categoryFilter.value;
        const sort     = sortFilter.value;

        if (query)    url.searchParams.set('q',        query);
        else          url.searchParams.delete('q');

        if (category) url.searchParams.set('categoria', category);
        else          url.searchParams.delete('categoria');

        if (sort && sort !== 'recent') url.searchParams.set('sort', sort);
        else                           url.searchParams.delete('sort');

        return url.toString();
    }

    function syncBrowserUrl() {
        const url = new URL(window.location.href);

        // Limpiamos los params de filtro anteriores
        ['q', 'categoria', 'sort'].forEach(k => url.searchParams.delete(k));

        const query    = searchInput.value.trim();
        const category = categoryFilter.value;
        const sort     = sortFilter.value;

        if (query)              url.searchParams.set('q',        query);
        if (category)           url.searchParams.set('categoria', category);
        if (sort && sort !== 'recent') url.searchParams.set('sort', sort);

        window.history.replaceState({}, '', url.toString());
    }

    async function fetchCatalog() {
        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();
        setLoadingState(true);

        const fetchUrl = buildFetchUrl();

        try {
            const response = await fetch(fetchUrl, {
                headers: { 'Accept': 'application/json' },
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            // Verificar que sea JSON antes de parsear
            const contentType = response.headers.get('Content-Type') || '';
            if (!contentType.includes('application/json')) {
                const raw = await response.text();
                console.error('[Catálogo AJAX] Respuesta no-JSON:', raw.substring(0, 600));
                throw new Error('El servidor no devolvió JSON.');
            }

            const payload = await response.json();
            results.innerHTML = payload.html ?? '';
            syncBrowserUrl();

        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('[Catálogo AJAX] Error:', err);
                results.innerHTML =
                    '<p class="text-center text-danger py-5 mb-0">' +
                    'No se pudieron cargar los productos. Intenta nuevamente.' +
                    '</p>';
            }
        } finally {
            setLoadingState(false);
        }
    }

    function queueFetch(delay = 0) {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(fetchCatalog, delay);
    }

    // Eventos de filtros
    searchInput.addEventListener('input',  () => queueFetch(280));
    categoryFilter.addEventListener('change', () => queueFetch());
    sortFilter.addEventListener('change',     () => queueFetch());

    clearBtn.addEventListener('click', function () {
        searchInput.value    = '';
        categoryFilter.value = '';
        sortFilter.value     = 'recent';
        queueFetch();
    });
});
</script>