<?php
$fullName = trim(($client['nombre'] ?? '') . ' ' . ($client['apellidos'] ?? ''));
?>

<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-xl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1"><?= htmlspecialchars($fullName !== '' ? $fullName : 'Perfil de cliente'); ?></h1>
                    <p class="text-muted mb-0">Perfil completo del cliente, historial de compras y rese&ntilde;as.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= site_url('admin/clientes'); ?>" class="btn btn-outline-primary btn-lg">Volver a clientes</a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card-modern p-4 bg-white h-100">
                        <p class="text-uppercase text-muted small mb-2">Total pedidos</p>
                        <h2 class="display-6 fw-bold mb-0"><?= (int) ($client['total_pedidos'] ?? 0); ?></h2>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-modern p-4 bg-white h-100">
                        <p class="text-uppercase text-muted small mb-2">&Uacute;ltimo pedido</p>
                        <?php if (!empty($client['ultimo_pedido_estatus'])): ?>
                            <span class="badge badge-state <?= htmlspecialchars($client['ultimo_pedido_estatus']); ?>">
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $client['ultimo_pedido_estatus']))); ?>
                            </span>
                            <p class="text-muted small mt-3 mb-0">
                                <?= !empty($client['ultimo_pedido_fecha']) ? date('d/m/Y H:i', strtotime($client['ultimo_pedido_fecha'])) : 'Sin fecha'; ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted mb-0">A&uacute;n no tiene pedidos.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-modern p-4 bg-white h-100">
                        <p class="text-uppercase text-muted small mb-2">Total gastado</p>
                        <h2 class="display-6 fw-bold text-primary mb-0">$<?= number_format((float) ($client['total_gastado'] ?? 0), 2); ?></h2>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="section-surface rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-3">Informaci&oacute;n del cliente</h5>
                        <div class="customer-detail-grid">
                            <div>
                                <span class="customer-detail-label">Nombre</span>
                                <div><?= htmlspecialchars($fullName !== '' ? $fullName : 'Sin nombre'); ?></div>
                            </div>
                            <div>
                                <span class="customer-detail-label">Correo</span>
                                <div><?= htmlspecialchars($client['email'] ?? 'Sin correo'); ?></div>
                            </div>
                            <div>
                                <span class="customer-detail-label">Tel&eacute;fono</span>
                                <div><?= htmlspecialchars($client['telefono'] ?: 'Sin telefono'); ?></div>
                            </div>
                            <div>
                                <span class="customer-detail-label">Registro</span>
                                <div><?= !empty($client['created_at']) ? date('d/m/Y H:i', strtotime($client['created_at'])) : 'Sin fecha'; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-surface rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-3">Direcciones</h5>
                        <?php if (!empty($addresses)): ?>
                            <div class="vstack gap-3">
                                <?php foreach ($addresses as $address): ?>
                                    <div class="customer-address-card rounded-4 p-3">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <strong><?= !empty($address['es_principal']) ? 'Direcci&oacute;n principal' : 'Direcci&oacute;n secundaria'; ?></strong>
                                            <span class="text-muted small"><?= htmlspecialchars($address['ciudad'] ?? ''); ?></span>
                                        </div>
                                        <div class="small text-muted">
                                            <?= htmlspecialchars(trim(($address['calle'] ?? '') . ' ' . ($address['numero_exterior'] ?? '') . ' ' . ($address['numero_interior'] ?? ''))); ?><br>
                                            <?= htmlspecialchars(($address['colonia'] ?? '') . ', ' . ($address['ciudad'] ?? '')); ?><br>
                                            <?= htmlspecialchars(($address['estado'] ?? '') . ', ' . ($address['pais'] ?? '')); ?>, CP <?= htmlspecialchars($address['codigo_postal'] ?? ''); ?>
                                            <?php if (!empty($address['referencias'])): ?>
                                                <br><?= htmlspecialchars($address['referencias']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">El cliente no tiene direcciones guardadas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card-modern p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Compras del cliente</h5>
                    <span class="text-muted small"><?= count($orders); ?> pedidos</span>
                </div>

                <?php if (!empty($orders)): ?>
                    <div class="vstack gap-4">
                        <?php foreach ($orders as $order): ?>
                            <div class="customer-order-card rounded-4 p-4">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                                    <div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            <h6 class="mb-0 fw-bold">Pedido #<?= htmlspecialchars($order['numero_pedido']); ?></h6>
                                            <span class="badge badge-state <?= htmlspecialchars($order['estado_pedido']); ?>">
                                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $order['estado_pedido']))); ?>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-1">Fecha: <?= date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                        <p class="text-muted small mb-0">Pago: <?= htmlspecialchars($order['mp_status'] ?: 'Sin registro'); ?></p>
                                    </div>
                                    <div class="text-lg-end">
                                        <div class="fw-semibold mb-2">$<?= number_format((float) $order['total'], 2); ?></div>
                                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                            <a href="<?= site_url('admin/pedido-detalle/' . (int) $order['id']); ?>" class="btn btn-sm btn-outline-primary">Ver detalle</a>
                                            <a href="<?= site_url('admin/pedido-ticket/' . (int) $order['id']); ?>" target="_blank" class="btn btn-sm btn-outline-dark">Abrir ticket</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-lg-6">
                                        <div class="customer-order-meta rounded-4 p-3 h-100">
                                            <h6 class="small text-uppercase text-muted mb-2">Env&iacute;o</h6>
                                            <div class="small">
                                                <?= htmlspecialchars($order['direccion_nombre_completo'] ?? 'No disponible'); ?><br>
                                                <?= htmlspecialchars(trim(($order['direccion_calle'] ?? '') . ' ' . ($order['direccion_numero_exterior'] ?? '') . ' ' . ($order['direccion_numero_interior'] ?? ''))); ?><br>
                                                <?= htmlspecialchars(($order['direccion_colonia'] ?? '') . ', ' . ($order['direccion_ciudad'] ?? '')); ?><br>
                                                <?= htmlspecialchars(($order['direccion_estado'] ?? '') . ', ' . ($order['direccion_pais'] ?? '')); ?>, CP <?= htmlspecialchars($order['direccion_codigo_postal'] ?? ''); ?><br>
                                                Tel: <?= htmlspecialchars($order['direccion_telefono'] ?? 'Sin telefono'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="customer-order-meta rounded-4 p-3 h-100">
                                            <h6 class="small text-uppercase text-muted mb-2">Resumen</h6>
                                            <div class="small">
                                                Art&iacute;culos: <?= (int) ($order['item_count'] ?? 0); ?><br>
                                                Subtotal: $<?= number_format((float) ($order['subtotal'] ?? 0), 2); ?><br>
                                                Env&iacute;o: $<?= number_format((float) ($order['costo_envio'] ?? 0), 2); ?><br>
                                                Estado de pago: <?= htmlspecialchars($order['mp_status_detail'] ?: ($order['mp_status'] ?: 'Sin detalle')); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="text-muted">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (($order['items'] ?? []) as $item): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($item['producto_nombre']); ?></strong>
                                                        <div class="text-muted small"><?= htmlspecialchars($item['producto_sku']); ?></div>
                                                    </td>
                                                    <td><?= (int) $item['cantidad']; ?></td>
                                                    <td>$<?= number_format((float) $item['precio_unitario'], 2); ?></td>
                                                    <td>$<?= number_format((float) $item['subtotal'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Este cliente a&uacute;n no ha realizado compras.</p>
                <?php endif; ?>
            </div>

            <div class="card-modern p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Rese&ntilde;as del cliente</h5>
                    <span class="text-muted small"><?= count($reviews); ?> rese&ntilde;as</span>
                </div>

                <?php if (!empty($reviews)): ?>
                    <div class="vstack gap-3">
                        <?php foreach ($reviews as $review): ?>
                            <div class="customer-review-card rounded-4 p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($review['producto_nombre'] ?? 'Producto'); ?></h6>
                                        <div class="small text-muted">
                                            <?= str_repeat('&#9733;', (int) ($review['calificacion'] ?? 0)); ?>
                                            <?= str_repeat('&#9734;', max(0, 5 - (int) ($review['calificacion'] ?? 0))); ?>
                                        </div>
                                    </div>
                                    <div class="text-md-end">
                                        <span class="badge bg-light text-dark"><?= htmlspecialchars(ucfirst($review['estatus'] ?? '')); ?></span>
                                        <div class="small text-muted mt-1"><?= !empty($review['created_at']) ? date('d/m/Y H:i', strtotime($review['created_at'])) : ''; ?></div>
                                    </div>
                                </div>
                                <?php if (!empty($review['titulo'])): ?>
                                    <p class="fw-semibold mb-1"><?= htmlspecialchars($review['titulo']); ?></p>
                                <?php endif; ?>
                                <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($review['comentario'] ?? '')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Este cliente no ha publicado rese&ntilde;as.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
