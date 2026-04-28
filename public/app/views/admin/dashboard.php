<?php
// views/admin/dashboard.php
$title = 'Dashboard Admin | Biomedcs Souls';
?>

<div class="container-fluid admin-panel py-5">
    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>

        <!-- Contenido Principal -->
        <div class="col-xl-9">

            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="display-5 fw-bold mb-1">Dashboard Admin</h1>
                    <p class="text-muted">Resumen general de la tienda Sensea</p>
                </div>
            </div>

            <!-- Tarjetas de Resumen -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card-modern p-4 bg-white h-100 text-center">
                        <p class="text-uppercase text-muted small mb-2">Productos</p>
                        <h2 class="display-6 fw-bold text-primary"><?= intval($productCount ?? 0); ?></h2>
                        <a href="<?= site_url('admin/productos'); ?>" class="btn btn-outline-primary btn-sm mt-3">
                            Gestionar productos
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-modern p-4 bg-white h-100 text-center">
                        <p class="text-uppercase text-muted small mb-2">Pedidos Totales</p>
                        <h2 class="display-6 fw-bold"><?= intval($totalOrders ?? 0); ?></h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-modern p-4 bg-white h-100 text-center">
                        <p class="text-uppercase text-muted small mb-2">Pedidos del Mes</p>
                        <h2 class="display-6 fw-bold text-success"><?= intval($currentMonthOrders ?? 0); ?></h2>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-modern p-4 bg-white h-100 text-center">
                        <p class="text-uppercase text-muted small mb-2">Ventas del Mes</p>
                        <h2 class="display-6 fw-bold text-success">
                            $<?= number_format($monthlySales ?? 0, 2); ?>
                        </h2>
                        <small class="text-muted">MXN</small>
                    </div>
                </div>
            </div>

            <!-- Ventas Totales -->
            <div class="card-modern p-4 mb-5 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ventas Totales HistÃ³ricas</h5>
                    <span class="h3 fw-bold text-primary">
                        $<?= number_format($totalSales ?? 0, 2); ?> MXN
                    </span>
                </div>
            </div>

            <!-- Pedidos Pendientes -->
            <div class="card-modern p-4 mb-5 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Pedidos Pendientes</h5>
                    <a href="<?= site_url('admin/pedidos'); ?>" class="btn btn-sm btn-outline-warning">Ver todos</a>
                </div>

                <?php if (!empty($pendingOrders)): ?>
                    <div class="list-group">
                        <?php foreach ($pendingOrders as $order): ?>
                            <a href="<?= site_url('admin/pedido-detalle/' . $order['id']); ?>"
                               class="list-group-item list-group-item-action rounded-4 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($order['numero_pedido'] ?? 'N/A'); ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($order['cliente_nombre'] ?? 'Sin nombre'); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning">Pendiente</span><br>
                                        <strong>$<?= number_format($order['total'] ?? 0, 2); ?></strong>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No hay pedidos pendientes en este momento.</p>
                <?php endif; ?>
            </div>

            <!-- Pedidos Entregados -->
            <div class="card-modern p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Pedidos Entregados Recientemente</h5>
                    <a href="<?= site_url('admin/pedidos'); ?>" class="btn btn-sm btn-outline-success">Ver todos</a>
                </div>

                <?php if (!empty($deliveredOrders)): ?>
                    <div class="list-group">
                        <?php foreach ($deliveredOrders as $order): ?>
                            <a href="<?= site_url('admin/pedido-detalle/' . $order['id']); ?>"
                               class="list-group-item list-group-item-action rounded-4 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($order['numero_pedido'] ?? 'N/A'); ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($order['cliente_nombre'] ?? 'Sin nombre'); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">Entregado</span><br>
                                        <strong>$<?= number_format($order['total'] ?? 0, 2); ?></strong>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No hay pedidos entregados recientemente.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>