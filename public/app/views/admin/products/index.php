<?php
// views/admin/products/index.php
$title = 'Productos - Admin | Biomedics Souls';
?>

<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-xl-9">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">Productos</h1>
                    <p class="text-muted mb-0">Busca, filtra y edita productos rápidamente desde el panel.</p>
                </div>
                <a href="<?= site_url('admin/producto-form'); ?>" class="btn btn-primary btn-lg">+ Nuevo producto</a>
            </div>

            <!-- === FORMULARIO CON AJAX SIMPLE === -->
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Buscar</label>
                    <input type="text" id="search" class="form-control rounded-4" 
                           placeholder="Nombre, slug o SKU" value="<?= htmlspecialchars($search ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select id="categoria" class="form-select rounded-4">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($selectedCategory ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estatus</label>
                    <select id="estatus" class="form-select rounded-4">
                        <option value="">Todos</option>
                        <option value="activo" <?= ($selectedStatus ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($selectedStatus ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        <option value="agotado" <?= ($selectedStatus ?? '') === 'agotado' ? 'selected' : '' ?>>Agotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select id="orden" class="form-select rounded-4">
                        <option value="updated_at_desc" <?= ($selectedSort ?? '') === 'updated_at_desc' ? 'selected' : '' ?>>Más recientes</option>
                        <option value="updated_at_asc" <?= ($selectedSort ?? '') === 'updated_at_asc' ? 'selected' : '' ?>>Más antiguos</option>
                        <option value="nombre_asc" <?= ($selectedSort ?? '') === 'nombre_asc' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                        <option value="nombre_desc" <?= ($selectedSort ?? '') === 'nombre_desc' ? 'selected' : '' ?>>Nombre (Z-A)</option>
                        <option value="precio_asc" <?= ($selectedSort ?? '') === 'precio_asc' ? 'selected' : '' ?>>Precio (bajo-alto)</option>
                        <option value="precio_desc" <?= ($selectedSort ?? '') === 'precio_desc' ? 'selected' : '' ?>>Precio (alto-bajo)</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="button" id="btnLimpiar" class="btn btn-outline-secondary btn-lg">Limpiar</button>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div id="productosContainer">
                <?php if (!empty($products)): ?>
                    <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
                        <table class="table mb-0 align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>SKU</th>
                                    <th>Precio</th>
                                    <th>Estatus</th>
                                    <th>Actualizado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= intval($product['id']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($product['imagen_principal'])): ?>
                                                    <img src="<?= asset_url('img/products/' . $product['imagen_principal']); ?>" alt="" class="rounded-2" style="width:48px;height:48px;object-fit:cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= htmlspecialchars($product['nombre']); ?></strong>
                                                    <div class="text-muted small"><?= htmlspecialchars($product['slug']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($product['categoria_nombre'] ?? '—'); ?></td>
                                        <td><?= htmlspecialchars($product['sku']); ?></td>
                                        <td>$<?= number_format($product['precio_descuento'] ?? $product['precio'], 2); ?></td>
                                        <td><span class="badge bg-<?= $product['estatus'] === 'agotado' ? 'danger' : 'success' ?>"><?= ucfirst($product['estatus'] ?? 'activo'); ?></span></td>
                                        <td><?= $product['updated_at'] ?? ''; ?></td>
                                        <td>
                                            <a href="<?= site_url('admin/producto-form/' . $product['id']); ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <a href="<?= site_url('admin/producto-eliminar/' . $product['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">Borrar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No se encontraron productos con esos filtros.</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
const baseUrl = '<?= site_url("admin/productos-filter"); ?>';
const assetUrl = '<?= asset_url("img/products/"); ?>';
let timeoutId;

function buildUrl() {
    const q = document.getElementById('search').value;
    const categoria = document.getElementById('categoria').value;
    const estatus = document.getElementById('estatus').value;
    const orden = document.getElementById('orden').value;
    
    const params = new URLSearchParams();
    if (q) params.append('q', q);
    if (categoria) params.append('categoria', categoria);
    if (estatus) params.append('estatus', estatus);
    params.append('orden', orden);
    
    return baseUrl + (params.toString() ? '?' + params.toString() : '');
}

function loadProducts() {
    const url = buildUrl();
    
    fetch(url)
        .then(res => res.json())
        .then(data => renderProducts(data.products))
        .catch(err => {
            console.error('Error:', err);
            document.getElementById('productosContainer').innerHTML = '<div class="alert alert-danger">Error al cargar productos</div>';
        });
}

function renderProducts(products) {
    const container = document.getElementById('productosContainer');
    
    if (!products || products.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No se encontraron productos con esos filtros.</div>';
        return;
    }
    
    let html = '<div class="table-responsive shadow-sm rounded-4 bg-white p-3">' +
        '<table class="table mb-0 align-middle">' +
        '<thead class="text-muted">' +
        '<tr>' +
        '<th>ID</th>' +
        '<th>Nombre</th>' +
        '<th>Categoría</th>' +
        '<th>SKU</th>' +
        '<th>Precio</th>' +
        '<th>Estatus</th>' +
        '<th>Actualizado</th>' +
        '<th></th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>';
    
    products.forEach(product => {
        const precio = product.precio_descuento || product.precio;
        const statusBadge = product.estatus === 'agotado' ? 'danger' : 'success';
        const statusText = (product.estatus || 'activo').charAt(0).toUpperCase() + (product.estatus || 'activo').slice(1);
        const imagen = product.imagen_principal ? assetUrl + product.imagen_principal : null;
        
        html += '<tr>' +
            '<td>' + parseInt(product.id) + '</td>' +
            '<td>' +
            '<div class="d-flex align-items-center gap-3">';
        
        if (imagen) {
            html += '<img src="' + imagen + '" alt="" class="rounded-2" style="width:48px;height:48px;object-fit:cover;">';
        } else {
            html += '<div class="bg-light rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="bi bi-image"></i></div>';
        }
        
        html += '<div>' +
            '<strong>' + escapeHtml(product.nombre) + '</strong>' +
            '<div class="text-muted small">' + escapeHtml(product.slug) + '</div>' +
            '</div>' +
            '</div>' +
            '</td>' +
            '<td>' + escapeHtml(product.categoria_nombre || '—') + '</td>' +
            '<td>' + escapeHtml(product.sku) + '</td>' +
            '<td>$' + parseFloat(precio).toFixed(2) + '</td>' +
            '<td><span class="badge bg-' + statusBadge + '">' + statusText + '</span></td>' +
            '<td>' + (product.updated_at || '') + '</td>' +
            '<td>' +
            '<a href="<?= site_url("admin/producto-form/"); ?>' + product.id + '" class="btn btn-sm btn-outline-primary">Editar</a> ' +
            '<a href="<?= site_url("admin/producto-eliminar/"); ?>' + product.id + '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar?\')">Borrar</a>' +
            '</td>' +
            '</tr>';
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function onFilterChange() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(loadProducts, 300);
}

function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('categoria').value = '';
    document.getElementById('estatus').value = '';
    document.getElementById('orden').value = 'updated_at_desc';
    loadProducts();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('search').addEventListener('input', onFilterChange);
    document.getElementById('categoria').addEventListener('change', onFilterChange);
    document.getElementById('estatus').addEventListener('change', onFilterChange);
    document.getElementById('orden').addEventListener('change', onFilterChange);
    document.getElementById('btnLimpiar').addEventListener('click', clearFilters);
});
</script>