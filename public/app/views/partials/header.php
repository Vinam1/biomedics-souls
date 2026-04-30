<header>
    <nav class="navbar navbar-expand-xl navbar-light site-navbar py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="<?= site_url(); ?>">
                <span class="brand-mark">
                    <img src="<?= asset_url('img/deco/logo.jpeg'); ?>" alt="Biomedics Souls Logo" height="44" width="44">
                </span>
                <span class="brand-copy">
                    <strong>Biomedics Souls</strong>
                    <small>Innovaci&oacute;n &amp; Vida</small>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-3 mb-xl-0 align-items-xl-center nav-primary">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('catalogo'); ?>">Cat&aacute;logo</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('ciencia'); ?>">Ciencia</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('faq'); ?>">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('quiz'); ?>">Quiz</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('contacto'); ?>">Contacto</a></li>
                </ul>

                <div class="d-flex flex-column flex-xl-row align-items-xl-center gap-3 ms-xl-auto">
                    <form action="<?= site_url('catalogo'); ?>" method="get" class="search-shell">
                        <input type="hidden" name="url" value="catalogo">
                        <i class="bi bi-search"></i>
                        <input type="search" name="q" class="form-control border-0 shadow-none" placeholder="Busca 'Vinagre de Manzana'">
                    </form>

                    <?php if (!empty($currentUser)): ?>
                        <?php if (in_array($currentUser['role'], ['admin', 'superadmin'], true)): ?>
                            <a class="btn btn-brand-soft" href="<?= site_url('admin/dashboard'); ?>">Admin</a>
                        <?php endif; ?>
                        <a class="btn btn-brand-soft" href="<?= site_url('cuenta'); ?>">Mi cuenta</a>
                        <span class="navbar-text user-chip"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario'); ?></span>
                        <form method="post" action="<?= site_url('auth/logout'); ?>" class="d-inline">
                            <?= csrf_input(); ?>
                            <button type="submit" class="btn btn-link nav-link p-0 border-0">Salir</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-brand" href="<?= site_url('auth/login'); ?>">Iniciar Sesi&oacute;n</a>
                    <?php endif; ?>

                    <a class="cart-link" href="<?= site_url('carrito'); ?>" aria-label="Ir al carrito">
                        <i class="bi bi-cart3"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
