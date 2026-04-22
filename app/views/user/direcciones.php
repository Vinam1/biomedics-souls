<?php
$editingAddress = $editingAddress ?? null;
?>

<h2 class="fw-bold mb-4">Mis Direcciones</h2>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <?php if (empty($addresses)): ?>
                <div class="text-center py-5">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;"><i class="fas fa-map-marker-alt fs-1 text-muted"></i></div>
                    <h5 class="text-muted mb-2">Aún no tienes direcciones guardadas</h5>
                    <p class="text-muted mb-0">Usa el formulario para agregar tu primera dirección de envío.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($addresses as $address): ?>
                        <div class="col-md-6">
                            <div class="card border h-100 rounded-4 p-4">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="fw-semibold mb-2"><?= !empty($address['es_principal']) ? 'Dirección principal' : 'Dirección guardada'; ?></h6>
                                        <p class="mb-1"><?= htmlspecialchars($address['calle']); ?> #<?= htmlspecialchars($address['numero_exterior']); ?><?= !empty($address['numero_interior']) ? ' Int. ' . htmlspecialchars($address['numero_interior']) : ''; ?></p>
                                        <p class="mb-1"><?= htmlspecialchars($address['colonia']); ?></p>
                                        <p class="mb-1"><?= htmlspecialchars($address['ciudad']); ?>, <?= htmlspecialchars($address['estado']); ?> <?= htmlspecialchars($address['codigo_postal']); ?></p>
                                        <p class="text-muted small mb-0"><?= htmlspecialchars($address['pais']); ?></p>
                                    </div>
                                    <?php if (!empty($address['es_principal'])): ?><span class="badge bg-primary">Principal</span><?php endif; ?>
                                </div>

                                <?php if (!empty($address['referencias'])): ?><p class="small text-muted mt-3 mb-0">Referencias: <?= htmlspecialchars($address['referencias']); ?></p><?php endif; ?>

                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <a href="<?= site_url('cuenta?tab=direcciones&edit_address=' . (int) $address['id']); ?>" class="btn btn-sm btn-outline-primary flex-fill">Editar</a>
                                    <form method="post" action="<?= site_url('cuenta/direccion-eliminar/' . (int) $address['id']); ?>" class="flex-fill">
                                        <?= csrf_input(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('¿Eliminar esta dirección?')">Eliminar</button>
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
            <h5 class="mb-4"><?= $editingAddress ? 'Editar dirección' : 'Agregar dirección'; ?></h5>
            <form method="post" action="<?= site_url('cuenta/direccion-guardar'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="address_id" value="<?= (int) ($editingAddress['id'] ?? 0); ?>">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Calle</label><input type="text" name="calle" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['calle'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Número exterior</label><input type="text" name="numero_exterior" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['numero_exterior'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Número interior</label><input type="text" name="numero_interior" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['numero_interior'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Colonia</label><input type="text" name="colonia" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['colonia'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Ciudad</label><input type="text" name="ciudad" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['ciudad'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Estado</label><input type="text" name="estado" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['estado'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">País</label><input type="text" name="pais" class="form-control rounded-3" value="<?= htmlspecialchars($editingAddress['pais'] ?? 'Mexico') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Código postal</label><input type="text" name="codigo_postal" class="form-control rounded-3" pattern="[0-9]{5}" maxlength="5" value="<?= htmlspecialchars($editingAddress['codigo_postal'] ?? '') ?>" required></div>
                    <div class="col-12"><label class="form-label">Referencias</label><textarea name="referencias" class="form-control rounded-3" rows="3"><?= htmlspecialchars($editingAddress['referencias'] ?? '') ?></textarea></div>
                    <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="es_principal" id="es_principal" <?= !empty($editingAddress['es_principal']) ? 'checked' : ''; ?>><label class="form-check-label" for="es_principal">Usar como dirección principal</label></div></div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill"><?= $editingAddress ? 'Actualizar dirección' : 'Guardar dirección'; ?></button>
                    <?php if ($editingAddress): ?><a href="<?= site_url('cuenta?tab=direcciones'); ?>" class="btn btn-outline-secondary">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
