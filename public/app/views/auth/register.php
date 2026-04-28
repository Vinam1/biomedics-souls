<div class="container" style="max-width: 560px;">
    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h1 class="h4 mb-3">Crear una cuenta</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php elseif (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('auth/register'); ?>">
                <?= csrf_input(); ?>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" minlength="8" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p class="mb-0">¿Ya tienes cuenta? <a href="<?= site_url('auth/login'); ?>">Inicia sesión</a></p>
            </div>
        </div>
    </div>
</div>
