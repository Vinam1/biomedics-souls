<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-xl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">Pedidos</h1>
                    <p class="text-muted mb-0">Visualiza y administra los pedidos recibidos.</p>
                </div>
                <a href="<?= site_url('admin/dashboard'); ?>" class="btn btn-outline-primary btn-lg">Volver al dashboard</a>
            </div>

            <?php if (!empty($orders)): ?>
                <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
                    <table class="table mb-0 align-middle">
                        <thead class="text-muted">
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['numero_pedido']); ?></td>
                                    <td><?= htmlspecialchars($order['cliente_nombre'] ?? 'Invitado'); ?></td>
                                    <td><?= intval($order['item_count']); ?></td>
                                    <td>$<?= number_format($order['total'], 2); ?></td>
                                    <td><span class="badge badge-state <?= htmlspecialchars($order['estado_pedido']); ?>"><?= htmlspecialchars(ucfirst($order['estado_pedido'])); ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                    <td><a href="<?= site_url('admin/pedido-detalle/' . $order['id']); ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No se encontraron pedidos.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
