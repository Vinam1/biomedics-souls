<div class="container" style="max-width: 520px;">
    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h1 class="h4 mb-3">Iniciar sesión</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('auth/login'); ?>">
                <?= csrf_input(); ?>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p class="mb-0">¿No tienes cuenta? <a href="<?= site_url('auth/register'); ?>">Regístrate</a></p>
            </div>
        </div>
    </div>
</div>
