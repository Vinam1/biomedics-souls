<?php
// views/user/pedidos.php
?>

    <h2 class="fw-bold mb-4">Mis Pedidos</h2>

<?php if (empty($orders)): ?>
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
        <i class="fas fa-shopping-bag fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">Aún no has realizado ningún pedido</h5>
        <p class="text-muted mb-4">Cuando hagas tu primera compra, aparecerá aquí.</p>
        <a href="<?= site_url('catalogo') ?>" class="btn btn-primary px-5">Ir al Catálogo</a>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th class="ps-4">Nº Pedido</th>
                    <th>Fecha</th>
                    <th>Productos</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="ps-4 fw-medium">#<?= htmlspecialchars($order['numero_pedido'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= $order['item_count'] ?? '1' ?> producto(s)</span>
                        </td>
                        <td class="text-end fw-bold">$<?= number_format($order['total'] ?? 0, 2) ?></td>
                        <td class="text-center">
                            <span class="badge px-3 py-2 rounded-pill bg-<?= ($order['estado_pedido'] ?? 'pendiente') === 'entregado' ? 'success' : 'warning' ?>">
                                <?= ucfirst($order['estado_pedido'] ?? 'Pendiente') ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?= site_url('pedido/detalle/' . (int) $order['id']); ?>" class="btn btn-sm btn-outline-primary">Ver detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
