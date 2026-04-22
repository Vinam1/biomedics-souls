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
                <a href="<?= site_url('admin/producto-form'); ?>" class="btn btn-primary btn-lg">Nuevo producto</a>
            </div>

            <form method="get" class="row g-3 mb-4 align-items-end">
                <div class="col-md-5"><label class="form-label">Buscar</label><input type="text" name="q" class="form-control rounded-4" placeholder="Nombre, slug o SKU" value="<?= htmlspecialchars($search); ?>"></div>
                <div class="col-md-3"><label class="form-label">Categoría</label><select name="categoria" class="form-select rounded-4"><option value="">Todas las categorías</option><?php foreach ($categories as $category): ?><option value="<?= intval($category['id']); ?>" <?= $selectedCategory == $category['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($category['nombre']); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-2"><label class="form-label">Estatus</label><select name="estatus" class="form-select rounded-4"><option value="">Todos</option><option value="activo" <?= $selectedStatus === 'activo' ? 'selected' : ''; ?>>Activo</option><option value="inactivo" <?= $selectedStatus === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option><option value="agotado" <?= $selectedStatus === 'agotado' ? 'selected' : ''; ?>>Agotado</option></select></div>
                <div class="col-md-2"><label class="form-label">Orden</label><select name="orden" class="form-select rounded-4"><option value="updated_at_desc" <?= $selectedSort === 'updated_at_desc' ? 'selected' : ''; ?>>Recientes</option><option value="updated_at_asc" <?= $selectedSort === 'updated_at_asc' ? 'selected' : ''; ?>>Antiguos</option><option value="precio_asc" <?= $selectedSort === 'precio_asc' ? 'selected' : ''; ?>>Precio ↑</option><option value="precio_desc" <?= $selectedSort === 'precio_desc' ? 'selected' : ''; ?>>Precio ↓</option></select></div>
                <div class="col-md-12 text-end"><button type="submit" class="btn btn-outline-primary btn-lg">Aplicar filtros</button></div>
            </form>

            <?php if (!empty($products)): ?>
                <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
                    <table class="table mb-0 align-middle">
                        <thead class="text-muted">
                            <tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>SKU</th><th>Precio</th><th>Estatus</th><th>Actualizado</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= intval($product['id']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($product['imagen_principal'])): ?>
                                                <img src="<?= asset_url('img/products/' . $product['imagen_principal']); ?>" alt="" class="rounded-2" style="width: 48px; height: 48px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;"><i class="bi bi-image text-muted"></i></div>
                                            <?php endif; ?>
                                            <div><strong><?= htmlspecialchars($product['nombre']); ?></strong><div class="text-muted small"><?= htmlspecialchars($product['slug']); ?></div></div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($product['categoria_nombre'] ?? 'General'); ?></td>
                                    <td><?= htmlspecialchars($product['sku']); ?></td>
                                    <td>
                                        <?php if ($product['precio_descuento'] !== null && $product['precio_descuento'] !== ''): ?>
                                            <span class="text-decoration-line-through text-muted">$<?= number_format($product['precio'], 2); ?></span>
                                            <span class="fw-bold ms-2 text-success">$<?= number_format($product['precio_descuento'], 2); ?></span>
                                        <?php else: ?>
                                            <span>$<?= number_format($product['precio'], 2); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-state <?= htmlspecialchars($product['estatus']); ?>"><?= htmlspecialchars(ucfirst($product['estatus'])); ?></span></td>
                                    <td><?= htmlspecialchars($product['updated_at'] ?? ''); ?></td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-end align-items-center">
                                            <a href="<?= site_url('admin/producto-form/' . $product['id']); ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <form action="<?= site_url('admin/producto-eliminar/' . $product['id']); ?>" method="post" class="d-inline">
                                                <?= csrf_input(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este producto? Esta acción puede restaurarse desde la base de datos.');">Borrar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No hay productos que coincidan con los filtros seleccionados.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
