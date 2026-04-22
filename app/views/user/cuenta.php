<?php
$title = 'Mi Cuenta | Biomedics Souls';
$tab = $tab ?? 'dashboard';
?>

<div class="container-fluid py-4" style="background: #f8f9fc; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-3 col-xl-2 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar bg-gradient rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 55px; height: 55px;">
                        <?= strtoupper(substr($user['nombre'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-semibold">Hola, <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?></h5>
                        <small class="text-muted">Miembro desde <?= isset($user['created_at']) ? date('Y', strtotime($user['created_at'])) : '2026' ?></small>
                    </div>
                </div>

                <nav class="nav flex-column gap-1">
                    <a href="?tab=dashboard" class="nav-link d-flex align-items-center gap-3 px-3 py-3 rounded-3 <?= $tab === 'dashboard' ? 'active bg-primary text-white' : 'text-dark' ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                    <a href="?tab=pedidos" class="nav-link d-flex align-items-center gap-3 px-3 py-3 rounded-3 <?= $tab === 'pedidos' ? 'active bg-primary text-white' : 'text-dark' ?>"><i class="fas fa-shopping-bag"></i><span>Mis Pedidos</span></a>
                    <a href="?tab=pagos" class="nav-link d-flex align-items-center gap-3 px-3 py-3 rounded-3 <?= $tab === 'pagos' ? 'active bg-primary text-white' : 'text-dark' ?>"><i class="fas fa-credit-card"></i><span>Métodos de Pago</span></a>
                    <a href="?tab=direcciones" class="nav-link d-flex align-items-center gap-3 px-3 py-3 rounded-3 <?= $tab === 'direcciones' ? 'active bg-primary text-white' : 'text-dark' ?>"><i class="fas fa-map-marker-alt"></i><span>Mis Direcciones</span></a>
                    <a href="?tab=config" class="nav-link d-flex align-items-center gap-3 px-3 py-3 rounded-3 <?= $tab === 'config' ? 'active bg-primary text-white' : 'text-dark' ?>"><i class="fas fa-cog"></i><span>Configuración</span></a>
                </nav>

                <hr class="my-4">

                <form method="post" action="<?= site_url('auth/logout') ?>" class="px-3 py-1">
                    <?= csrf_input(); ?>
                    <button type="submit" class="nav-link text-danger d-flex align-items-center gap-3 px-0 py-2 rounded-3 btn btn-link text-start w-100 border-0">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-9 col-xl-10">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= ($flash['type'] ?? 'info') === 'success' ? 'success' : 'danger'; ?> rounded-4 mb-4">
                    <?= htmlspecialchars($flash['message'] ?? ''); ?>
                </div>
            <?php endif; ?>

            <?php
            if ($tab === 'dashboard') {
                include __DIR__ . '/dashboard.php';
            } elseif ($tab === 'pedidos') {
                include __DIR__ . '/pedidos.php';
            } elseif ($tab === 'pagos') {
                include __DIR__ . '/pagos.php';
            } elseif ($tab === 'direcciones') {
                include __DIR__ . '/direcciones.php';
            } elseif ($tab === 'config') {
                include __DIR__ . '/config.php';
            }
            ?>
        </div>
    </div>
</div>
