<h2 class="fw-bold mb-4">Configuración de Cuenta</h2>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-5">
            <h5 class="mb-4">Información Personal</h5>

            <form method="post" action="<?= site_url('cuenta/perfil'); ?>">
                <?= csrf_input(); ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-medium">Nombre</label><input type="text" name="nombre" class="form-control rounded-3" value="<?= htmlspecialchars($user['nombre'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-medium">Apellidos</label><input type="text" name="apellidos" class="form-control rounded-3" value="<?= htmlspecialchars($user['apellidos'] ?? '') ?>" required></div>
                    <div class="col-12"><label class="form-label fw-medium">Correo electrónico</label><input type="email" name="email" class="form-control rounded-3" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required></div>
                    <div class="col-12"><label class="form-label fw-medium">Teléfono</label><input type="tel" name="telefono" class="form-control rounded-3" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>" pattern="[0-9]{10}" maxlength="10" placeholder="5512345678"></div>
                </div>

                <div class="mt-4"><button type="submit" class="btn btn-primary px-5">Guardar cambios</button></div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-5 h-100">
            <h5 class="mb-4">Datos útiles para tus compras</h5>
            <div class="small text-muted mb-4">
                <p class="mb-2">Desde esta sección puedes mantener actualizado tu teléfono y datos básicos.</p>
                <p class="mb-0">Tus direcciones y métodos de pago se gestionan desde sus pestañas y estarán disponibles en checkout.</p>
            </div>

            <hr class="my-4">

            <div class="small text-muted">
                <p class="mb-1"><strong>Cuenta creada:</strong> <?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></p>
                <p class="mb-0"><strong>Correo actual:</strong> <?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
            </div>
        </div>
    </div>
</div>
