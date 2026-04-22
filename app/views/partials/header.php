<header>
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= site_url(); ?>">
                <img src="<?= asset_url('img/deco/logo.jpeg'); ?>" alt="Biomedics Souls Logo" height="40" class="me-2">
                Biomedics Souls
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="#mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('home'); ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('catalogo'); ?>">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('ciencia'); ?>">Investigación</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('faq'); ?>">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('contacto'); ?>">Contacto</a></li>
                    <?php if (!empty($currentUser)): ?>
                        <?php if (in_array($currentUser['role'], ['admin', 'superadmin'], true)): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= site_url('admin/dashboard'); ?>">Admin</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('cuenta'); ?>">Mi cuenta</a></li>
                        <li class="nav-item"><span class="nav-link disabled px-3"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario'); ?></span></li>
                        <li class="nav-item">
                            <form method="post" action="<?= site_url('auth/logout'); ?>" class="d-inline">
                                <?= csrf_input(); ?>
                                <button type="submit" class="nav-link btn btn-link p-0 border-0">Salir</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('auth/login'); ?>">Ingresar</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('auth/register'); ?>">Registro</a></li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link header-action" href="<?= site_url('carrito'); ?>">Carrito</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
