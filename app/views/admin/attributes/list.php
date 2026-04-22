<?php
$config = [
    'icon' => 'fa-tags',
    'color' => 'text-warning',
    'plural' => 'Etiquetas',
    'singular' => 'Etiqueta'
];

$title = 'Gestión de Etiquetas';
?>

<div class="container-fluid py-5 admin-attributes" style="min-height: 100vh; background: #f8f9fa;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-5">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= site_url('admin'); ?>" class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center" style="width:52px; height:52px; font-size:1.5rem;">←</a>
                    <div>
                        <h1 class="h2 fw-bold mb-1">Gestión de Etiquetas</h1>
                        <p class="text-muted mb-0">Administra las etiquetas visuales para los productos.</p>
                    </div>
                </div>
                <a href="<?= site_url('admin/atributo-form/etiquetas'); ?>" class="btn btn-lg btn-gradient rounded-4 px-4"><i class="fas fa-plus me-2"></i>Agregar Etiqueta</a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-12 col-md-6"><label class="form-label fw-semibold">Buscar</label><input type="text" name="q" class="form-control rounded-4" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($search ?? ''); ?>"></div>
                        <div class="col-12 col-md-3 col-lg-2"><button type="submit" class="btn btn-primary w-100 rounded-4"><i class="fas fa-search me-2"></i>Buscar</button></div>
                        <?php if (!empty($search)): ?><div class="col-12 col-md-3 col-lg-2"><a href="<?= site_url('admin/atributos/etiquetas'); ?>" class="btn btn-outline-secondary w-100 rounded-4"><i class="fas fa-times me-2"></i>Limpiar</a></div><?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <?php if (empty($items)): ?>
                        <div class="text-center py-5"><i class="fas fa-tags text-warning fs-1 mb-3 d-block opacity-50"></i><h5 class="text-muted">No hay etiquetas registradas</h5><p class="text-muted mb-4">Comienza creando la primera etiqueta.</p><a href="<?= site_url('admin/atributo-form/etiquetas'); ?>" class="btn btn-primary rounded-4"><i class="fas fa-plus me-2"></i>Crear Etiqueta</a></div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light"><tr><th>Nombre</th><th>Slug</th><th class="text-end">Productos</th><th>Color</th><th class="text-end">Acciones</th></tr></thead>
                                <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($item['nombre']); ?></strong></td>
                                        <td><code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($item['slug']); ?></code></td>
                                        <td class="text-end"><?php if (($item['usages'] ?? 0) > 0): ?><span class="badge bg-success rounded-pill"><?= $item['usages']; ?> producto<?= $item['usages'] !== 1 ? 's' : ''; ?></span><?php else: ?><span class="badge bg-secondary rounded-pill">Sin usar</span><?php endif; ?></td>
                                        <td><div class="d-flex align-items-center gap-2"><div class="rounded-circle" style="width: 24px; height: 24px; background-color: <?= htmlspecialchars($item['color'] ?? '#3B82F6'); ?>;" title="<?= htmlspecialchars($item['color'] ?? '#3B82F6'); ?>"></div><code class="text-muted"><?= htmlspecialchars($item['color'] ?? '#3B82F6'); ?></code></div></td>
                                        <td class="text-end"><a href="<?= site_url('admin/atributo-form/etiquetas/' . (int) $item['id']); ?>" class="btn btn-sm btn-outline-primary rounded-3" title="Editar"><i class="fas fa-edit"></i></a><button type="button" class="btn btn-sm btn-outline-danger rounded-3 ms-2" onclick="deleteAttribute(this, <?= (int) $item['id']; ?>, 'etiquetas', <?= $item['usages'] ?? 0; ?>)" title="Eliminar"><i class="fas fa-trash"></i></button></td>
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
const attributeCsrfToken = <?= json_encode($csrfToken ?? csrf_token()); ?>;

async function deleteAttribute(btn, id, type, usages) {
    if (usages > 0) {
        alert('No se puede eliminar porque está siendo usado por ' + usages + ' producto(s).');
        return;
    }

    if (!confirm('¿Estás seguro de que deseas eliminar esta etiqueta?')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const response = await fetch('<?= site_url("admin/atributo-eliminar"); ?>/' + type + '/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ csrf_token: attributeCsrfToken })
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
