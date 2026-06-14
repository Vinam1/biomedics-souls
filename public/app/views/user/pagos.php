<?php
$editingPayment = $editingPayment ?? null;
$paymentTypes = [
    'tarjeta' => 'Tarjeta',
    'openpay' => 'OpenPay',
    'spei' => 'SPEI',
    'oxxo' => 'OXXO',
    'transferencia' => 'Transferencia',
    'otro' => 'Otro',
];
?>

<h2 class="fw-bold mb-4">Métodos de Pago</h2>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <?php if (empty($paymentMethods)): ?>
                <div class="text-center py-5">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;"><i class="fas fa-credit-card fs-1 text-muted"></i></div>
                    <h5 class="text-muted mb-2">Aún no tienes métodos de pago guardados</h5>
                    <p class="text-muted mb-0">Agrega un método de pago usando el formulario de la derecha para que puedas usarlo en el checkout.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($paymentMethods as $method): ?>
                        <div class="col-md-6">
                            <div class="card border h-100 rounded-4 p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $method['tipo']))); ?></h6>
                                        <p class="text-muted small mb-0"><?= htmlspecialchars($method['nickname'] ?? 'Método de pago'); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= !empty($method['es_predeterminado']) ? 'primary' : 'secondary' ?>"><?= !empty($method['es_predeterminado']) ? 'Predeterminado' : 'Guardado' ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($method['brand']) || !empty($method['ultimo_cuatro'])): ?>
                                    <p class="mb-1">
                                        <?= !empty($method['brand']) ? htmlspecialchars($method['brand']) . ' ' : ''; ?>
                                        <?= !empty($method['ultimo_cuatro']) ? '•••• ' . htmlspecialchars($method['ultimo_cuatro']) : ''; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($method['tipo_tarjeta'])): ?>
                                    <p class="text-muted small mb-3">Tarjeta de <?= htmlspecialchars($method['tipo_tarjeta']); ?></p>
                                <?php endif; ?>

                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <a href="<?= site_url('cuenta/pagos') . '&edit_payment=' . (int) $method['id']; ?>" class="btn btn-sm btn-outline-primary flex-fill">Editar</a>
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
            <h5 class="mb-4"><?= $editingPayment ? 'Editar método de pago' : 'Agregar método de pago'; ?></h5>
            <?php if ($editingPayment): ?>
                <div class="alert alert-info py-2 mb-4">Modifica los datos del método y guarda los cambios para actualizarlo.</div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('cuenta/pago-guardar'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="payment_id" value="<?= (int) ($editingPayment['id'] ?? 0); ?>">

                <div class="mb-3">
                    <label class="form-label">Tipo de pago</label>
                    <select name="tipo" class="form-select" required>
                        <?php foreach ($paymentTypes as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($editingPayment['tipo'] ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alias</label>
                    <input type="text" name="nickname" class="form-control rounded-3" value="<?= htmlspecialchars($editingPayment['nickname'] ?? '') ?>" placeholder="Ej. Tarjeta personal" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Marca</label>
                        <input type="text" name="brand" class="form-control rounded-3" value="<?= htmlspecialchars($editingPayment['brand'] ?? '') ?>" placeholder="Ej. Visa">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Últimos 4 dígitos</label>
                        <input type="text" name="ultimo_cuatro" class="form-control rounded-3" value="<?= htmlspecialchars($editingPayment['ultimo_cuatro'] ?? '') ?>" pattern="[0-9]{4}" maxlength="4" placeholder="1234">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo de tarjeta</label>
                        <select name="tipo_tarjeta" class="form-select">
                            <option value="" <?= empty($editingPayment['tipo_tarjeta']) ? 'selected' : '' ?>>Selecciona</option>
                            <option value="credito" <?= ($editingPayment['tipo_tarjeta'] ?? '') === 'credito' ? 'selected' : '' ?>>Crédito</option>
                            <option value="debito" <?= ($editingPayment['tipo_tarjeta'] ?? '') === 'debito' ? 'selected' : '' ?>>Débito</option>
                        </select>
                    </div>
                </div>

                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="es_predeterminado" id="es_predeterminado" <?= !empty($editingPayment['es_predeterminado']) ? 'checked' : '' ?> >
                    <label class="form-check-label" for="es_predeterminado">Marcar como método predeterminado</label>
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" id="activo" <?= $editingPayment === null || !empty($editingPayment['activo']) ? 'checked' : '' ?> >
                    <label class="form-check-label" for="activo">Activo</label>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill"><?= $editingPayment ? 'Actualizar método' : 'Guardar método'; ?></button>
                    <?php if ($editingPayment): ?><a href="<?= site_url('cuenta/pagos'); ?>" class="btn btn-outline-secondary">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
