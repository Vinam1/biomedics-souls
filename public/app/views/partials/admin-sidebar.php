<?php
function sidebarActive(string $segment): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return preg_match('#' . preg_quote($segment, '#') . '(/|$|\?)#', $uri) ? 'active-admin' : '';
}
?>
<aside class="admin-sidebar p-4 rounded-4 shadow-sm bg-dark position-sticky top-4"
       style="min-height: calc(100vh - 3rem);">

    <div class="mb-5 text-white">
        <a href="<?= site_url('admin/dashboard'); ?>"
           class="text-decoration-none d-flex align-items-center gap-2">
            <span class="fs-4 fw-bold">Biomedics</span>
            <small class="text-muted">Admin</small>
        </a>
    </div>

    <nav class="nav flex-column gap-2">
        <a href="<?= site_url('admin/dashboard'); ?>"
           class="nav-link text-white px-3 py-2 rounded-4 <?= sidebarActive('/admin/dashboard'); ?>">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>

        <a href="<?= site_url('admin/productos'); ?>"
           class="nav-link text-white px-3 py-2 rounded-4 <?= sidebarActive('/admin/productos'); ?>">
            <i class="fas fa-box me-2"></i> Productos
        </a>

        <a href="<?= site_url('admin/categorias'); ?>"
           class="nav-link text-white px-3 py-2 rounded-4 <?= sidebarActive('/admin/categorias'); ?>">
            <i class="fas fa-folder me-2"></i> Categorías
        </a>

        <a href="<?= site_url('admin/atributos/etiquetas'); ?>"
           class="nav-link text-white px-3 py-2 rounded-4 <?= sidebarActive('/admin/atributos'); ?>">
            <i class="fas fa-tags me-2"></i> Etiquetas
        </a>

        <a href="<?= site_url('admin/pedidos'); ?>"
           class="nav-link text-white px-3 py-2 rounded-4 <?= sidebarActive('/admin/pedidos'); ?>">
            <i class="fas fa-shopping-cart me-2"></i> Pedidos
        </a>
    </nav>

    <div class="mt-5 text-white-50 small">
        <p class="mb-1">Med-Tech Premium</p>
        <p class="mb-0">Diseño enfocado en confianza, ciencia y bienestar.</p>
    </div>
</aside>
