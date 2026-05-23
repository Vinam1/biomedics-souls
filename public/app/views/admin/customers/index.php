<?php
$filters = $filters ?? ['q' => '', 'status' => ''];
$statusOptions = $statusOptions ?? [];
$page = (int) ($page ?? 1);
$totalPages = (int) ($totalPages ?? 1);
$totalClients = (int) ($totalClients ?? count($clients ?? []));
?>

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

            <div class="card-modern p-4 bg-white mb-4">
                <form method="get" action="<?= site_url('admin/clientes'); ?>" class="row g-3 align-items-end">
                    <input type="hidden" name="url" value="admin/clientes">
                    <div class="col-lg-6">
                        <label for="q" class="form-label small text-uppercase text-muted fw-bold">Buscar cliente</label>
                        <input type="text" id="q" name="q" value="<?= htmlspecialchars($filters['q'] ?? ''); ?>" class="form-control rounded-4" placeholder="Nombre, correo o teléfono">
                    </div>
                    <div class="col-lg-3">
                        <label for="status" class="form-label small text-uppercase text-muted fw-bold">Último estatus</label>
                        <select id="status" name="status" class="form-select rounded-4">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= htmlspecialchars((string) $value); ?>" <?= (($filters['status'] ?? '') === (string) $value) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $label))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill flex-grow-1">Filtrar</button>
                        <a href="<?= site_url('admin/clientes'); ?>" class="btn btn-outline-secondary rounded-pill">Limpiar</a>
                    </div>
                </form>
                <div class="text-muted small mt-3">
                    <?= $totalClients; ?> cliente<?= $totalClients === 1 ? '' : 's'; ?> encontrado<?= $totalClients === 1 ? '' : 's'; ?>.
                </div>
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

                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4" aria-label="Paginación de clientes">
                        <ul class="pagination justify-content-center">
                            <?php
                            $queryBase = [
                                'url' => 'admin/clientes',
                                'q' => $filters['q'] ?? '',
                                'status' => $filters['status'] ?? '',
                            ];
                            ?>
                            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link rounded-pill mx-1" href="<?= $page <= 1 ? '#' : site_url('admin/clientes') . '&q=' . urlencode((string) ($filters['q'] ?? '')) . '&status=' . urlencode((string) ($filters['status'] ?? '')) . '&page=' . ($page - 1); ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link rounded-pill mx-1" href="<?= site_url('admin/clientes') . '&q=' . urlencode((string) ($filters['q'] ?? '')) . '&status=' . urlencode((string) ($filters['status'] ?? '')) . '&page=' . $i; ?>">
                                        <?= $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link rounded-pill mx-1" href="<?= $page >= $totalPages ? '#' : site_url('admin/clientes') . '&q=' . urlencode((string) ($filters['q'] ?? '')) . '&status=' . urlencode((string) ($filters['status'] ?? '')) . '&page=' . ($page + 1); ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">No se encontraron clientes registrados.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
