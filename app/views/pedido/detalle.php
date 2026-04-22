<?php
$order = $order ?? [];
$items = $items ?? [];
$transaction = $transaction ?? null;
?>

<div class="container py-5" style="max-width: 980px;">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div>
            <h1 class="h3 mb-2">Detalle del pedido #<?= htmlspecialchars($order['numero_pedido'] ?? ''); ?></h1>
            <p class="text-muted mb-0">Consulta los productos, el estado de pago y descarga tu ticket cuando lo necesites.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= site_url('pedido/ticket/' . (int) ($order['id'] ?? 0)); ?>" target="_blank" class="btn btn-outline-dark">Abrir ticket PDF</a>
            <a href="<?= site_url('cuenta?tab=pedidos'); ?>" class="btn btn-outline-primary">Volver a mis pedidos</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Artículos comprados</h5>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between gap-3 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($item['producto_nombre'] ?? ''); ?></div>
                                <div class="small text-muted">SKU <?= htmlspecialchars($item['producto_sku'] ?? ''); ?></div>
                                <div class="small text-muted">Cantidad: <?= (int) ($item['cantidad'] ?? 0); ?></div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">$<?= number_format((float) ($item['precio_unitario'] ?? 0), 2); ?> c/u</div>
                                <div class="fw-semibold">$<?= number_format((float) ($item['subtotal'] ?? 0), 2); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Pago</h5>
                    <div class="small text-muted mb-1">Estado</div>
                    <div class="fw-semibold mb-3"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $order['estado_pedido'] ?? 'pendiente'))); ?></div>
                    <div class="small text-muted mb-1">Pasarela</div>
                    <div class="fw-semibold mb-3"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $transaction['gateway'] ?? ($order['tipo_metodo_pago'] ?? 'manual')))); ?></div>
                    <div class="small text-muted mb-1">Referencia</div>
                    <div class="fw-semibold mb-3"><?= htmlspecialchars($transaction['referencia'] ?? ($order['mp_payment_id'] ?? 'N/D')); ?></div>
                    <div class="small text-muted mb-1">Total</div>
                    <div class="fs-4 fw-bold">$<?= number_format((float) ($order['total'] ?? 0), 2); ?></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Entrega</h5>
                    <p class="mb-1 fw-semibold"><?= htmlspecialchars($order['direccion_nombre_completo'] ?? ''); ?></p>
                    <p class="mb-1 text-muted"><?= htmlspecialchars(($order['direccion_calle'] ?? '') . ' #' . ($order['direccion_numero_exterior'] ?? '')); ?></p>
                    <?php if (!empty($order['direccion_numero_interior'])): ?>
                        <p class="mb-1 text-muted">Int. <?= htmlspecialchars($order['direccion_numero_interior']); ?></p>
                    <?php endif; ?>
                    <p class="mb-1 text-muted"><?= htmlspecialchars(($order['direccion_colonia'] ?? '') . ', ' . ($order['direccion_ciudad'] ?? '')); ?></p>
                    <p class="mb-1 text-muted"><?= htmlspecialchars(($order['direccion_estado'] ?? '') . ', ' . ($order['direccion_pais'] ?? '') . ' ' . ($order['direccion_codigo_postal'] ?? '')); ?></p>
                    <?php if (!empty($order['direccion_telefono'])): ?>
                        <p class="mb-0 text-muted">Tel. <?= htmlspecialchars($order['direccion_telefono']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
