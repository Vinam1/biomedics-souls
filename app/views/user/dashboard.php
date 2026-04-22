<?php
// views/user/dashboard.php
?>

<div class="row">
    <div class="col-12">
        <h2 class="fw-bold mb-1">Hola, <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?></h2>
        <p class="text-muted mb-4">Bienvenido de nuevo a tu panel de salud.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-2 small">Pedidos Activos</p>
                    <h2 class="fw-bold mb-0"><?= count($orders ?? []) ?></h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="fas fa-box fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-2 small">Métodos de Pago</p>
                    <h2 class="fw-bold mb-0"><?= count($paymentMethods ?? []) ?></h2>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="fas fa-credit-card fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mi Panel de Control -->
<h5 class="mb-3 fw-semibold">Mi Panel de Control</h5>
<p class="text-muted mb-4">Accede rápidamente a tus secciones más importantes.</p>

<div class="row g-4">
    <div class="col-md-4">
        <a href="<?= site_url('catalogo') ?>" class="card border-0 shadow-sm rounded-4 p-4 text-decoration-none h-100 hover-lift">
            <div class="text-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-shopping-cart fs-3"></i>
                </div>
                <h6 class="fw-semibold">Comprar de nuevo</h6>
                <p class="text-muted small mb-0">Repite tus compras anteriores</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?= site_url('quiz') ?>" class="card border-0 shadow-sm rounded-4 p-4 text-decoration-none h-100 hover-lift">
            <div class="text-center">
                <div class="bg-info bg-opacity-10 text-info rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-clipboard-list fs-3"></i>
                </div>
                <h6 class="fw-semibold">Resultados de mi Quiz</h6>
                <p class="text-muted small mb-0">Ver tu fórmula personalizada</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="?tab=pedidos" class="card border-0 shadow-sm rounded-4 p-4 text-decoration-none h-100 hover-lift">
            <div class="text-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-truck fs-3"></i>
                </div>
                <h6 class="fw-semibold">Rastrear mi pedido</h6>
                <p class="text-muted small mb-0">Estado de tus envíos</p>
            </div>
        </a>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold">Actividad Reciente</h5>
        <a href="?tab=pedidos" class="text-primary small fw-medium">Ver todo →</a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <p class="text-muted mb-0">No tienes actividad reciente.</p>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4">
            <?php foreach (array_slice($orders, 0, 4) as $order): ?>
                <div class="p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Pedido #<?= htmlspecialchars($order['numero_pedido'] ?? 'N/A') ?></strong><br>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($order['created_at'])) ?></small>
                        </div>
                        <div class="text-end">
                        <span class="badge bg-<?= ($order['estado_pedido'] ?? '') === 'entregado' ? 'success' : 'warning' ?> px-3 py-1">
                            <?= ucfirst($order['estado_pedido'] ?? 'Pendiente') ?>
                        </span>
                            <div class="mt-1 fw-bold">$<?= number_format($order['total'] ?? 0, 2) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-lift:hover {
        transform: translateY(-4px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
</style>