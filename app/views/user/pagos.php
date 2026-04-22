<?php
$editingPaymentMethod = $editingPaymentMethod ?? null;
?>

<h2 class="fw-bold mb-4">Métodos de Pago</h2>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <?php if (empty($paymentMethods)): ?>
                <div class="text-center py-5">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;"><i class="fas fa-credit-card fs-1 text-muted"></i></div>
                    <h5 class="text-muted mb-2">Aún no tienes métodos de pago</h5>
                    <p class="text-muted mb-0">Guarda referencias de pago para seleccionarlas más rápido al comprar.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($paymentMethods as $method): ?>
                        <div class="col-md-6">
                            <div class="card border h-100 rounded-4 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="fas fa-credit-card fs-2 text-primary"></i>
                                        <h6 class="mt-3 mb-1"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $method['tipo']))); ?></h6>
                                        <?php if (!empty($method['nickname'])): ?><p class="text-muted small mb-1"><?= htmlspecialchars($method['nickname']); ?></p><?php endif; ?>
                                        <p class="text-muted small mb-0"><?= !empty($method['brand']) ? htmlspecialchars($method['brand']) . ' ' : ''; ?><?= !empty($method['ultimo_cuatro']) ? '•••• ' . htmlspecialchars($method['ultimo_cuatro']) : 'Sin dígitos registrados'; ?></p>
                                    </div>
                                    <?php if (!empty($method['es_predeterminado'])): ?><span class="badge bg-primary">Predeterminado</span><?php endif; ?>
                                </div>
                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <a href="<?= site_url('cuenta?tab=pagos&edit_payment=' . (int) $method['id']); ?>" class="btn btn-sm btn-outline-primary flex-fill">Editar</a>
                                    <form method="post" action="<?= site_url('cuenta/pago-eliminar/' . (int) $method['id']); ?>" class="flex-fill">
                                        <?= csrf_input(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('¿Eliminar este método de pago?')">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 1.5rem;">
            <h5 class="mb-4"><?= $editingPaymentMethod ? 'Editar método de pago' : 'Agregar método de pago'; ?></h5>
            <form method="post" action="<?= site_url('cuenta/pago-guardar'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="payment_id" value="<?= (int) ($editingPaymentMethod['id'] ?? 0); ?>">
                <input type="hidden" name="activo" value="1">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Tipo</label><select name="tipo" class="form-select rounded-3" required><option value="">Selecciona</option><?php foreach (['tarjeta' => 'Tarjeta', 'mercado_pago' => 'Mercado Pago', 'spei' => 'SPEI', 'oxxo' => 'OXXO', 'transferencia' => 'Transferencia', 'otro' => 'Otro'] as $value => $label): ?><option value="<?= $value; ?>" <?= ($editingPaymentMethod['tipo'] ?? '') === $value ? 'selected' : ''; ?>><?= $label; ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><label class="form-label">Alias o referencia</label><input type="text" name="nickname" class="form-control rounded-3" value="<?= htmlspecialchars($editingPaymentMethod['nickname'] ?? '') ?>" placeholder="Ej. Tarjeta principal"></div>
                    <div class="col-md-6"><label class="form-label">Marca</label><input type="text" name="brand" class="form-control rounded-3" value="<?= htmlspecialchars($editingPaymentMethod['brand'] ?? '') ?>" placeholder="Visa, Mastercard, MP"></div>
                    <div class="col-md-6"><label class="form-label">Últimos 4 dígitos</label><input type="text" name="ultimo_cuatro" class="form-control rounded-3" maxlength="4" pattern="[0-9]{4}" value="<?= htmlspecialchars($editingPaymentMethod['ultimo_cuatro'] ?? '') ?>" placeholder="1234"></div>
                    <div class="col-12"><label class="form-label">Tipo de tarjeta</label><select name="tipo_tarjeta" class="form-select rounded-3"><option value="">No aplica</option><option value="credito" <?= ($editingPaymentMethod['tipo_tarjeta'] ?? '') === 'credito' ? 'selected' : ''; ?>>Crédito</option><option value="debito" <?= ($editingPaymentMethod['tipo_tarjeta'] ?? '') === 'debito' ? 'selected' : ''; ?>>Débito</option></select></div>
                    <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="es_predeterminado" id="es_predeterminado" <?= !empty($editingPaymentMethod['es_predeterminado']) ? 'checked' : ''; ?>><label class="form-check-label" for="es_predeterminado">Usar como método predeterminado</label></div></div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill"><?= $editingPaymentMethod ? 'Actualizar método' : 'Guardar método'; ?></button>
                    <?php if ($editingPaymentMethod): ?><a href="<?= site_url('cuenta?tab=pagos'); ?>" class="btn btn-outline-secondary">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
