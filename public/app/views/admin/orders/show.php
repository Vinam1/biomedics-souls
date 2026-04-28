<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-xl-9">
            <div class="card-modern p-4 bg-white shadow-sm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h1 class="display-6 fw-bold mb-1">Pedido <?= htmlspecialchars($order['numero_pedido']); ?></h1>
                        <p class="text-muted mb-0">InformaciÃ³n detallada del pedido.</p>
                    </div>
                    <a href="<?= site_url('admin/pedidos'); ?>" class="btn btn-outline-primary btn-lg">Volver a pedidos</a>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="section-surface rounded-4 p-4 h-100">
                            <h6 class="text-uppercase text-muted small mb-3">Cliente</h6>
                            <p class="mb-1 fw-semibold"><?= htmlspecialchars($order['cliente_nombre'] ?? 'Invitado'); ?></p>
                            <p class="text-muted mb-1"><?= htmlspecialchars($order['cliente_email'] ?? 'Sin correo'); ?></p>
                            <p class="text-muted small mb-0">Estado: <span class="fw-semibold"><?= htmlspecialchars($order['estado_pedido']); ?></span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-surface rounded-4 p-4 h-100">
                            <h6 class="text-uppercase text-muted small mb-3">Resumen del pedido</h6>
                            <p class="mb-1"><strong>Total:</strong> $<?= number_format($order['total'], 2); ?></p>
                            <p class="text-muted mb-1"><strong>Subtotal:</strong> $<?= number_format($order['subtotal'], 2); ?></p>
                            <p class="text-muted mb-0"><strong>EnvÃ­o:</strong> $<?= number_format($order['costo_envio'], 2); ?></p>
                        </div>
                    </div>
                </div>

                <div class="section-surface rounded-4 p-4 mb-4">
                    <h6 class="text-uppercase text-muted small mb-3">DirecciÃ³n de envÃ­o</h6>
                    <p class="mb-1"><?= htmlspecialchars($order['direccion_nombre_completo'] ?? 'No disponible'); ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['direccion_calle'] ?? ''); ?> <?= htmlspecialchars($order['direccion_numero_exterior'] ?? ''); ?> <?= htmlspecialchars($order['direccion_numero_interior'] ?? ''); ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['direccion_colonia'] ?? ''); ?>, <?= htmlspecialchars($order['direccion_ciudad'] ?? ''); ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['direccion_estado'] ?? ''); ?>, <?= htmlspecialchars($order['direccion_pais'] ?? ''); ?></p>
                    <p class="mb-0 text-muted">CP: <?= htmlspecialchars($order['direccion_codigo_postal'] ?? ''); ?></p>
                </div>

                <div class="table-responsive rounded-4 shadow-sm bg-white p-3">
                    <table class="table mb-0 align-middle">
                        <thead class="text-muted">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($item['producto_nombre']); ?></strong>
                                            <div class="text-muted small"><?= htmlspecialchars($item['producto_sku']); ?></div>
                                        </div>
                                    </td>
                                    <td><?= intval($item['cantidad']); ?></td>
                                    <td>$<?= number_format($item['precio_unitario'], 2); ?></td>
                                    <td>$<?= number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
