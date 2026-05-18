<div class="container-fluid admin-panel py-5">
    <div class="row g-4">
        <div class="col-xl-3 d-none d-xl-block">
            <?php require APPROOT . '/views/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-xl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">Clientes</h1>
                    <p class="text-muted mb-0">Consulta perfiles, ubicaci&oacute;n y el estado del &uacute;ltimo pedido de cada cliente.</p>
                </div>
                <a href="<?= site_url('admin/dashboard'); ?>" class="btn btn-outline-primary btn-lg">Volver al dashboard</a>
            </div>

            <?php if (!empty($clients)): ?>
                <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
                    <table class="table mb-0 align-middle">
                        <thead class="text-muted">
                            <tr>
                                <th>Cliente</th>
                                <th>Tel&eacute;fono</th>
                                <th>Municipio</th>
                                <th>&Uacute;ltimo pedido</th>
                                <th>Total pedidos</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <?php $fullName = trim(($client['nombre'] ?? '') . ' ' . ($client['apellidos'] ?? '')); ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($fullName !== '' ? $fullName : 'Sin nombre'); ?></strong>
                                            <div class="text-muted small"><?= htmlspecialchars($client['email'] ?? ''); ?></div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($client['telefono'] ?: 'Sin telefono'); ?></td>
                                    <td><?= htmlspecialchars($client['municipio'] ?: 'Sin registro'); ?></td>
                                    <td>
                                        <?php if (!empty($client['ultimo_pedido_estatus'])): ?>
                                            <span class="badge badge-state <?= htmlspecialchars($client['ultimo_pedido_estatus']); ?>">
                                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $client['ultimo_pedido_estatus']))); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin pedidos</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int) ($client['total_pedidos'] ?? 0); ?></td>
                                    <td>
                                        <a href="<?= site_url('admin/cliente-detalle/' . (int) $client['id']); ?>" class="btn btn-sm btn-outline-primary">
                                            Ver perfil
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No se encontraron clientes registrados.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
