<?php
$title = 'Carrito de Compras | Biomedcs Souls - Sensea';
?>

<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-4 mb-5">
        <div>
            <h1 class="display-5 fw-bold mb-1">Tu Carrito</h1>
            <p class="text-muted">Revisa tus productos antes de proceder al pago.</p>
        </div>
        <a href="<?= site_url('catalogo'); ?>" class="btn btn-outline-primary btn-lg">
            Seguir comprando
        </a>
    </div>

    <?php if (!empty($cartItems)): ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cartItems as $item):
                                $product    = $item['product'];
                                $unitPrice  = $product['precio_descuento'] ?? $product['precio'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="flex-shrink-0 bg-light rounded-3 overflow-hidden"
                                                 style="width:80px; height:80px;">
                                                <?php if (!empty($product['imagen_principal'])): ?>
                                                    <img src="<?= asset_url('img/products/' . htmlspecialchars($product['imagen_principal'])); ?>"
                                                         alt="<?= htmlspecialchars($product['nombre']); ?>"
                                                         class="img-fluid w-100 h-100 object-fit-cover">
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                        <i class="bi bi-image fs-3"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold mb-1"><?= htmlspecialchars($product['nombre']); ?></h6>
                                                <small class="text-muted">SKU: <?= htmlspecialchars($product['sku'] ?? 'N/A'); ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center border rounded-3">
                                            <form action="<?= site_url('carrito/actualizar/' . $product['id']); ?>"
                                                  method="post" class="d-inline">
                                                <?= csrf_input(); ?>
                                                <input type="hidden" name="quantity"
                                                       value="<?= max(1, $item['quantity'] - 1); ?>">
                                                <button type="submit"
                                                        class="btn btn-link text-dark px-3 py-1">−</button>
                                            </form>

                                            <span class="px-3 fw-semibold"><?= $item['quantity']; ?></span>

                                            <form action="<?= site_url('carrito/actualizar/' . $product['id']); ?>"
                                                  method="post" class="d-inline">
                                                <?= csrf_input(); ?>
                                                <input type="hidden" name="quantity"
                                                       value="<?= $item['quantity'] + 1; ?>">
                                                <button type="submit"
                                                        class="btn btn-link text-dark px-3 py-1">+</button>
                                            </form>
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <span class="h6 fw-bold">$<?= number_format($unitPrice, 2); ?></span>
                                    </td>

                                    <td class="text-end">
                                        <span class="h6 fw-bold">$<?= number_format($item['subtotal'], 2); ?></span>
                                    </td>

                                    <td class="text-end">
                                        <form action="<?= site_url('carrito/remover/' . $product['id']); ?>"
                                              method="post" class="d-inline">
                                            <?= csrf_input(); ?>
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-3"
                                                    onclick="return confirm('¿Eliminar este producto del carrito?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-panel p-4 sticky-top rounded-4 shadow-sm" style="top: 1.5rem;">
                    <h4 class="mb-4">Resumen del pedido</h4>

                    <!-- BUG FIX: $total is always passed by CartController; removed the
                         direct Cart::getTotal() fallback call that was a model call in a view. -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <strong>$<?= number_format($total, 2); ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Envío estimado</span>
                        <strong class="text-success">Gratis</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Total a pagar</span>
                        <span class="fs-4 fw-bold text-primary">$<?= number_format($total, 2); ?></span>
                    </div>

                    <a href="<?= site_url('checkout'); ?>"
                       class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-semibold">
                        Proceder al pago
                    </a>

                    <p class="text-muted small text-center mt-3">Pago seguro • Envío rápido</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x display-1 text-muted"></i>
            </div>
            <h3 class="fw-bold mb-3">Tu carrito está vacío</h3>
            <p class="text-muted mb-4">Agrega productos premium Sensea para comenzar.</p>
            <a href="<?= site_url('catalogo'); ?>"
               class="btn btn-primary btn-lg px-5 rounded-4">
                Explorar Catálogo
            </a>
        </div>
    <?php endif; ?>
</div>