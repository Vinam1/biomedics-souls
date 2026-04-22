<?php
$title = 'Checkout | Biomedics Souls';
$step = $step ?? 1;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div class="step-item <?= $step >= 1 ? 'active' : ''; ?>"><div class="step-circle">1</div><span>Dirección</span></div>
                    <div class="step-item <?= $step >= 2 ? 'active' : ''; ?>"><div class="step-circle">2</div><span>Pago</span></div>
                    <div class="step-item <?= $step >= 3 ? 'active' : ''; ?>"><div class="step-circle">3</div><span>Confirmar</span></div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <?php if ($step === 1): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-5">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <h3 class="mb-1">Selecciona una dirección</h3>
                                    <p class="text-muted mb-0">Usaremos tu teléfono de perfil: <strong><?= htmlspecialchars($user['telefono'] ?? 'Sin capturar'); ?></strong></p>
                                </div>
                                <a href="<?= site_url('cuenta?tab=direcciones'); ?>" class="btn btn-outline-primary">Administrar direcciones</a>
                            </div>

                            <?php if (empty($addresses)): ?>
                                <div class="alert alert-warning rounded-4 mb-0">Primero guarda una dirección en tu cuenta para continuar con el checkout.</div>
                            <?php else: ?>
                                <form method="post" action="<?= site_url('checkout?step=1'); ?>">
                                    <?= csrf_input(); ?>
                                    <div class="vstack gap-3">
                                        <?php foreach ($addresses as $address): ?>
                                            <label class="border rounded-4 p-4 d-flex gap-3 align-items-start <?= !empty($selectedAddress['id']) && (int) $selectedAddress['id'] === (int) $address['id'] ? 'border-primary bg-light' : ''; ?>">
                                                <input type="radio" name="address_id" value="<?= (int) $address['id']; ?>" class="form-check-input mt-1" <?= !empty($selectedAddress['id']) && (int) $selectedAddress['id'] === (int) $address['id'] ? 'checked' : ''; ?>>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <strong><?= htmlspecialchars($address['calle']); ?> #<?= htmlspecialchars($address['numero_exterior']); ?></strong>
                                                        <?php if (!empty($address['es_principal'])): ?><span class="badge bg-primary">Principal</span><?php endif; ?>
                                                    </div>
                                                    <div class="text-muted small"><?= htmlspecialchars($address['colonia']); ?>, <?= htmlspecialchars($address['ciudad']); ?>, <?= htmlspecialchars($address['estado']); ?> <?= htmlspecialchars($address['codigo_postal']); ?></div>
                                                    <div class="text-muted small"><?= htmlspecialchars($address['pais']); ?></div>
                                                    <?php if (!empty($address['referencias'])): ?><div class="small mt-2">Referencias: <?= htmlspecialchars($address['referencias']); ?></div><?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="mt-4"><button type="submit" class="btn btn-primary btn-lg px-5">Continuar al pago →</button></div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($step === 2): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-5">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <h3 class="mb-1">Selecciona un método de pago</h3>
                                    <p class="text-muted mb-0">Puedes administrarlos desde tu cuenta y reutilizarlos aquí.</p>
                                </div>
                                <a href="<?= site_url('cuenta?tab=pagos'); ?>" class="btn btn-outline-primary">Administrar pagos</a>
                            </div>

                            <?php if (empty($paymentMethods)): ?>
                                <div class="alert alert-warning rounded-4 mb-0">Primero guarda un método de pago en tu cuenta para continuar.</div>
                            <?php else: ?>
                                <form method="post" action="<?= site_url('checkout?step=2'); ?>">
                                    <?= csrf_input(); ?>
                                    <div class="vstack gap-3">
                                        <?php foreach ($paymentMethods as $method): ?>
                                            <label class="border rounded-4 p-4 d-flex gap-3 align-items-start <?= !empty($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === (int) $method['id'] ? 'border-primary bg-light' : ''; ?>">
                                                <input type="radio" name="payment_id" value="<?= (int) $method['id']; ?>" class="form-check-input mt-1" <?= !empty($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === (int) $method['id'] ? 'checked' : ''; ?>>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $method['tipo']))); ?></strong>
                                                        <?php if (!empty($method['es_predeterminado'])): ?><span class="badge bg-primary">Predeterminado</span><?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($method['nickname'])): ?><div class="small mb-1"><?= htmlspecialchars($method['nickname']); ?></div><?php endif; ?>
                                                    <div class="text-muted small"><?= !empty($method['brand']) ? htmlspecialchars($method['brand']) . ' ' : ''; ?><?= !empty($method['ultimo_cuatro']) ? '•••• ' . htmlspecialchars($method['ultimo_cuatro']) : 'Sin dígitos registrados'; ?></div>
                                                    <?php if (!empty($method['tipo_tarjeta'])): ?><div class="text-muted small">Tarjeta de <?= htmlspecialchars($method['tipo_tarjeta']); ?></div><?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="mt-4 d-flex gap-3">
                                        <a href="<?= site_url('checkout?step=1'); ?>" class="btn btn-outline-secondary">← Volver</a>
                                        <button type="submit" class="btn btn-primary btn-lg px-5">Continuar a confirmación →</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($step === 3): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-5">
                            <h3 class="mb-4">Confirma tu pedido</h3>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <h6 class="mb-2">Dirección de envío</h6>
                                    <?php if (!empty($selectedAddress)): ?>
                                        <p class="text-muted mb-0">
                                            <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?><br>
                                            Tel. <?= htmlspecialchars($user['telefono'] ?? 'Sin capturar'); ?><br>
                                            <?= htmlspecialchars($selectedAddress['calle']); ?> #<?= htmlspecialchars($selectedAddress['numero_exterior']); ?><?= !empty($selectedAddress['numero_interior']) ? ' Int. ' . htmlspecialchars($selectedAddress['numero_interior']) : ''; ?><br>
                                            <?= htmlspecialchars($selectedAddress['colonia']); ?>, <?= htmlspecialchars($selectedAddress['ciudad']); ?><br>
                                            <?= htmlspecialchars($selectedAddress['estado']); ?>, <?= htmlspecialchars($selectedAddress['pais']); ?> <?= htmlspecialchars($selectedAddress['codigo_postal']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2">Método de pago</h6>
                                    <?php if (!empty($selectedPaymentMethod)): ?>
                                        <p class="text-muted mb-0">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $selectedPaymentMethod['tipo']))); ?><br>
                                            <?= !empty($selectedPaymentMethod['nickname']) ? htmlspecialchars($selectedPaymentMethod['nickname']) . '<br>' : ''; ?>
                                            <?= !empty($selectedPaymentMethod['brand']) ? htmlspecialchars($selectedPaymentMethod['brand']) . ' ' : ''; ?><?= !empty($selectedPaymentMethod['ultimo_cuatro']) ? '•••• ' . htmlspecialchars($selectedPaymentMethod['ultimo_cuatro']) : ''; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <h6 class="mb-3">Resumen del pedido</h6>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between border-bottom py-2"><div><strong><?= htmlspecialchars($item['product']['nombre'] ?? '') ?></strong><div class="text-muted small">x<?= $item['quantity'] ?></div></div><strong>$<?= number_format($item['subtotal'], 2) ?></strong></div>
                            <?php endforeach; ?>

                            <div class="d-flex justify-content-between mt-4 mb-2"><span>Subtotal</span><strong>$<?= number_format($total ?? 0, 2) ?></strong></div>
                            <div class="d-flex justify-content-between mb-3"><span>Envío</span><strong class="text-success">Gratis</strong></div>
                            <hr>
                            <div class="d-flex justify-content-between fs-4 fw-bold"><span>Total a pagar</span><span>$<?= number_format($total ?? 0, 2) ?></span></div>

                            <div class="mt-5 d-flex gap-3">
                                <a href="<?= site_url('checkout?step=2'); ?>" class="btn btn-outline-secondary">← Volver</a>
                                <form method="post" action="<?= site_url('pedido/confirmar'); ?>" class="flex-grow-1">
                                    <?= csrf_input(); ?>
                                    <button type="submit" class="btn btn-success btn-lg w-100 py-3">Confirmar y Pagar</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <div class="checkout-panel p-4 sticky-top" style="top: 1.5rem;">
                        <h5 class="mb-3">Resumen del pedido</h5>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between mb-2"><div class="small"><?= htmlspecialchars($item['product']['nombre'] ?? '') ?><span class="text-muted"> ×<?= $item['quantity'] ?></span></div><strong class="small">$<?= number_format($item['subtotal'], 2) ?></strong></div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between"><span class="text-muted">Total</span><span class="fs-5 fw-bold">$<?= number_format($total ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-item { text-align: center; flex: 1; min-width: 120px; }
.step-circle { width: 32px; height: 32px; background: #e9ecef; color: #6c757d; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: bold; }
.step-item.active .step-circle { background: #0d6efd; color: white; }
</style>
