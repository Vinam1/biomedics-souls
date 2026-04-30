<?php
$order = $order ?? null;
$items = $items ?? [];
$transaction = $transaction ?? null;
$isPaid = ($order['estado_pedido'] ?? '') === 'pagado';
?>

<div class="container py-5" style="max-width: 960px;">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <h1 class="h3 <?= $isPaid ? 'text-success' : 'text-warning'; ?> mb-2">
                        <?= $isPaid ? 'Pago realizado con éxito' : 'Pedido creado y pago pendiente'; ?>
                    </h1>
                    <p class="mb-0 text-muted">
                        Pedido <strong>#<?= htmlspecialchars($order['numero_pedido'] ?? ''); ?></strong>
                        registrado el <?= !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : ''; ?>.
                    </p>
                </div>
                <?php if (!empty($order['id'])): ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= site_url('pedido/ticket/' . (int) $order['id']); ?>" target="_blank" class="btn btn-outline-dark"><i class="bi bi-file-earmark-pdf me-2"></i>Abrir ticket PDF</a>
                        <a href="<?= site_url('pedido/detalle/' . (int) $order['id']); ?>" class="btn btn-primary">Ver detalle</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="border rounded-4 p-4 h-100">
                        <h5 class="mb-3">Resumen de pago</h5>
                        <div class="small text-muted mb-2">Estado</div>
                        <div class="fw-semibold mb-3"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $order['estado_pedido'] ?? 'pendiente'))); ?></div>
                        <div class="small text-muted mb-2">Referencia</div>
                        <div class="fw-semibold mb-3"><?= htmlspecialchars($transaction['referencia'] ?? ($order['mp_payment_id'] ?? 'N/D')); ?></div>
                        <div class="small text-muted mb-2">Pasarela</div>
                        <div class="fw-semibold mb-3"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $transaction['gateway'] ?? ($order['tipo_metodo_pago'] ?? 'manual')))); ?></div>
                        <div class="small text-muted mb-2">Total</div>
                        <div class="fs-4 fw-bold">$<?= number_format((float) ($order['total'] ?? 0), 2); ?></div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="border rounded-4 p-4 h-100">
                        <h5 class="mb-3">Envío</h5>
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

            <div class="border rounded-4 p-4">
                <h5 class="mb-3">Productos</h5>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold"><?= htmlspecialchars($item['producto_nombre'] ?? ''); ?></div>
                            <div class="small text-muted">SKU <?= htmlspecialchars($item['producto_sku'] ?? ''); ?> · <?= (int) ($item['cantidad'] ?? 0); ?> pieza(s)</div>
                        </div>
                        <div class="text-end fw-semibold">$<?= number_format((float) ($item['subtotal'] ?? 0), 2); ?></div>
                    </div>
                <?php endforeach; ?>

                <div class="d-flex justify-content-between mt-4"><span>Subtotal</span><strong>$<?= number_format((float) ($order['subtotal'] ?? 0), 2); ?></strong></div>
                <div class="d-flex justify-content-between mt-2"><span>Envío</span><strong>$<?= number_format((float) ($order['costo_envio'] ?? 0), 2); ?></strong></div>
                <div class="d-flex justify-content-between mt-3 fs-5"><span>Total</span><strong>$<?= number_format((float) ($order['total'] ?? 0), 2); ?></strong></div>
            </div>

            <div class="d-flex gap-2 flex-wrap mt-4">
                <a href="<?= site_url('cuenta?tab=pedidos'); ?>" class="btn btn-outline-primary">Ir a mis pedidos</a>
                <a href="<?= site_url('catalogo'); ?>" class="btn btn-primary">Seguir comprando</a>
            </div>
        </div>
    </div>
</div>
