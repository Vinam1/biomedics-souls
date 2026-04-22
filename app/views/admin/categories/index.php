<?php
$title = 'Gestión de Categorías | Biomedics Souls';
?>

<div class="container-fluid py-5 admin-attributes" style="min-height: 100vh; background: #f8f9fa;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-5">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= site_url('admin'); ?>" class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center" style="width:52px; height:52px; font-size:1.5rem;">←</a>
                    <div>
                        <h1 class="h2 fw-bold mb-1">Gestión de Categorías</h1>
                        <p class="text-muted mb-0">Organiza tus productos por categorías.</p>
                    </div>
                </div>
                <a href="<?= site_url('admin/categoria-form'); ?>" class="btn btn-lg btn-gradient rounded-4 px-4"><i class="fas fa-plus me-2"></i>Agregar Categoría</a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-12 col-md-6"><label class="form-label fw-semibold">Buscar categoría</label><input type="text" name="q" class="form-control rounded-4" placeholder="Nombre de la categoría..." value="<?= htmlspecialchars($search ?? ''); ?>"></div>
                        <div class="col-12 col-md-3 col-lg-2"><button type="submit" class="btn btn-primary w-100 rounded-4"><i class="fas fa-search me-2"></i>Buscar</button></div>
                        <?php if (!empty($search)): ?><div class="col-12 col-md-3 col-lg-2"><a href="<?= site_url('admin/categorias'); ?>" class="btn btn-outline-secondary w-100 rounded-4"><i class="fas fa-times me-2"></i>Limpiar</a></div><?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <?php if (empty($categories)): ?>
                        <div class="text-center py-5"><i class="fas fa-folder text-info fs-1 mb-3 d-block opacity-50"></i><h5 class="text-muted">No hay categorías registradas</h5><p class="text-muted mb-4">Comienza creando la primera categoría.</p><a href="<?= site_url('admin/categoria-form'); ?>" class="btn btn-primary rounded-4"><i class="fas fa-plus me-2"></i>Crear Categoría</a></div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light"><tr><th>Nombre</th><th>Slug</th><th class="text-end">Productos</th><th class="text-end">Acciones</th></tr></thead>
                                <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($cat['nombre']); ?></strong></td>
                                        <td><code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($cat['slug']); ?></code></td>
                                        <td class="text-end"><?php if (($cat['product_count'] ?? 0) > 0): ?><span class="badge bg-success rounded-pill"><?= $cat['product_count']; ?> producto<?= $cat['product_count'] !== 1 ? 's' : ''; ?></span><?php else: ?><span class="badge bg-secondary rounded-pill">Sin productos</span><?php endif; ?></td>
                                        <td class="text-end">
                                            <a href="<?= site_url('admin/categoria-form/' . (int) $cat['id']); ?>" class="btn btn-sm btn-outline-primary rounded-3" title="Editar"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 ms-2" onclick="deleteCategory(this, <?= (int) $cat['id']; ?>, <?= $cat['product_count'] ?? 0; ?>)" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const categoryCsrfToken = <?= json_encode($csrfToken ?? csrf_token()); ?>;

async function deleteCategory(btn, id, productCount) {
    if (productCount > 0) {
        alert('No se puede eliminar esta categoría porque tiene ' + productCount + ' producto(s) asignados.');
        return;
    }

    if (!confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const response = await fetch('<?= site_url("admin/categoria-eliminar"); ?>/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ csrf_token: categoryCsrfToken })
        });

        const data = await response.json();

        if (data.success) {
            const row = btn.closest('tr');
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        } else {
            alert(data.message || 'Error al eliminar');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash"></i>';
    }
}
</script>

<link rel="stylesheet" href="<?= asset_url('css/admin-attributes.css'); ?>">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
