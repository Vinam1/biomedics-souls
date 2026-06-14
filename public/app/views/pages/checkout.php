<?php
$title = 'Checkout | Biomedics Souls';
$step = $step ?? 1;
?>

<section class="page-shell py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="checkout-steps mb-5" data-animate="fade-up">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div class="step-item <?= $step >= 1 ? 'active' : ''; ?>">
                            <div class="step-circle">1</div>
                            <span>Direccion</span>
                        </div>
                        <div class="step-item <?= $step >= 2 ? 'active' : ''; ?>">
                            <div class="step-circle">2</div>
                            <span>Pago</span>
                        </div>
                        <div class="step-item <?= $step >= 3 ? 'active' : ''; ?>">
                            <div class="step-circle">3</div>
                            <span>Confirmar</span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-7" data-animate="fade-up">
                        <?php if ($step === 1): ?>
                            <div class="section-card p-4 p-lg-5">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                                    <div>
                                        <h3 class="mb-1">Selecciona una direccion</h3>
                                        <p class="text-muted mb-0">Usaremos tu telefono de perfil: <strong><?= htmlspecialchars($user['telefono'] ?? 'Sin capturar'); ?></strong></p>
                                    </div>
                                    <a href="<?= site_url('cuenta/direcciones'); ?>" class="btn btn-outline-primary">Administrar direcciones</a>
                                </div>

                                <?php if (empty($addresses)): ?>
                                    <div class="alert alert-warning rounded-4 mb-0">Primero guarda una direccion en tu cuenta para continuar con el checkout.</div>
                                <?php else: ?>
                                    <form method="post" action="<?= site_url('checkout?step=1'); ?>">
                                        <?= csrf_input(); ?>
                                        <div class="vstack gap-3">
                                            <?php foreach ($addresses as $index => $address): ?>
                                                <label class="checkout-choice <?= !empty($selectedAddress['id']) && (int) $selectedAddress['id'] === (int) $address['id'] ? 'is-active' : ''; ?>" data-animate="fade-up" data-animate-delay="<?= $index * 70; ?>">
                                                    <input type="radio" name="address_id" value="<?= (int) $address['id']; ?>" class="form-check-input mt-1" <?= !empty($selectedAddress['id']) && (int) $selectedAddress['id'] === (int) $address['id'] ? 'checked' : ''; ?>>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <strong><?= htmlspecialchars($address['calle']); ?> #<?= htmlspecialchars($address['numero_exterior']); ?></strong>
                                                            <?php if (!empty($address['es_principal'])): ?><span class="badge bg-primary">Principal</span><?php endif; ?>
                                                        </div>
                                                        <div class="text-muted small"><?= htmlspecialchars($address['colonia']); ?>, <?= htmlspecialchars($address['ciudad']); ?>, <?= htmlspecialchars($address['estado']); ?> <?= htmlspecialchars($address['codigo_postal']); ?></div>
                                                        <div class="text-muted small"><?= htmlspecialchars($address['pais']); ?></div>
                                                        <?php if (!empty($address['referencias'])): ?><div class="small mt-2">Referencias: <?= htmlspecialchars($address['referencias']); ?></div><?php endif; ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mt-4"><button type="submit" class="btn btn-primary btn-lg px-5">Continuar al pago</button></div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($step === 2): ?>
                            <div class="section-card p-4 p-lg-5">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                                    <div>
                                        <h3 class="mb-1">Selecciona un metodo de pago</h3>
                                        <p class="text-muted mb-0">Usa una tarjeta guardada o paga con una tarjeta nueva dentro de OpenPay.</p>
                                    </div>
                                    <a href="<?= site_url('cuenta/pagos'); ?>" class="btn btn-outline-primary">Administrar pagos</a>
                                </div>

                                <form method="post" action="<?= site_url('checkout?step=2'); ?>">
                                    <?= csrf_input(); ?>
                                    <div class="vstack gap-3">
                                        <?php if (empty($paymentMethods)): ?>
                                            <div class="alert alert-info rounded-4 mb-1">Aun no tienes metodos guardados. Puedes continuar pagando con una tarjeta nueva.</div>
                                        <?php else: ?>
                                            <?php foreach ($paymentMethods as $index => $method): ?>
                                                <label class="checkout-choice <?= !empty($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === (int) $method['id'] ? 'is-active' : ''; ?>" data-animate="fade-up" data-animate-delay="<?= $index * 70; ?>">
                                                    <input type="radio" name="payment_id" value="<?= (int) $method['id']; ?>" class="form-check-input mt-1" <?= !empty($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === (int) $method['id'] ? 'checked' : ''; ?>>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $method['tipo']))); ?></strong>
                                                            <?php if (!empty($method['es_predeterminado'])): ?><span class="badge bg-primary">Predeterminado</span><?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($method['nickname'])): ?><div class="small mb-1"><?= htmlspecialchars($method['nickname']); ?></div><?php endif; ?>
                                                        <div class="text-muted small"><?= !empty($method['brand']) ? htmlspecialchars($method['brand']) . ' ' : ''; ?><?= !empty($method['ultimo_cuatro']) ? '**** ' . htmlspecialchars($method['ultimo_cuatro']) : 'Sin digitos registrados'; ?></div>
                                                        <?php if (!empty($method['tipo_tarjeta'])): ?><div class="text-muted small">Tarjeta de <?= htmlspecialchars($method['tipo_tarjeta']); ?></div><?php endif; ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <label class="checkout-choice <?= !empty($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === 0 ? 'is-active' : ''; ?>" data-animate="fade-up" data-animate-delay="<?= count($paymentMethods) * 70; ?>">
                                            <input type="radio" name="payment_id" value="new_card" class="form-check-input mt-1" <?= !empty($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === 0 ? 'checked' : ''; ?>>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                                    <strong>Tarjeta nueva</strong>
                                                    <span class="badge bg-success">OpenPay</span>
                                                    <span class="badge bg-light text-dark border">Visa</span>
                                                    <span class="badge bg-light text-dark border">Mastercard</span>
                                                </div>
                                                <div class="text-muted small">Capturas tus datos en el siguiente paso y se tokenizan de forma segura antes de procesar el cobro.</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="mt-4 d-flex gap-3 flex-wrap">
                                        <a href="<?= site_url('checkout?step=1'); ?>" class="btn btn-outline-secondary">Volver</a>
                                        <button type="submit" class="btn btn-primary btn-lg px-5">Continuar a confirmacion</button>
                                    </div>
                                </form>
                            </div>
                        <?php elseif ($step === 3): ?>
                            <div class="section-card p-4 p-lg-5">
                                <h3 class="mb-4">Confirma tu pedido</h3>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Direccion de envio</h6>
                                        <?php if (!empty($selectedAddress)): ?>
                                            <p class="text-muted mb-0">
                                                <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?><br>
                                                Tel. <?= htmlspecialchars($user['telefono'] ?? 'Sin capturar'); ?><br>
                                                <?= htmlspecialchars($selectedAddress['calle']); ?> #<?= htmlspecialchars($selectedAddress['numero_exterior']); ?><?= !empty($selectedAddress['numero_interior']) ? ' Int. ' . htmlspecialchars($selectedAddress['numero_interior']) : ''; ?><br>
                                                <?= htmlspecialchars($selectedAddress['colonia']); ?>, <?= htmlspecialchars($selectedAddress['ciudad']); ?><br>
                                                <?= htmlspecialchars($selectedAddress['estado']); ?>, <?= htmlspecialchars($selectedAddress['pais']); ?> <?= htmlspecialchars($selectedAddress['codigo_postal']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Metodo de pago</h6>
                                        <?php if (!empty($selectedPaymentMethod)): ?>
                                            <p class="text-muted mb-0">
                                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $selectedPaymentMethod['tipo']))); ?><br>
                                                <?= !empty($selectedPaymentMethod['nickname']) ? htmlspecialchars($selectedPaymentMethod['nickname']) . '<br>' : ''; ?>
                                                <?= !empty($selectedPaymentMethod['brand']) ? htmlspecialchars($selectedPaymentMethod['brand']) . ' ' : ''; ?><?= !empty($selectedPaymentMethod['ultimo_cuatro']) ? '**** ' . htmlspecialchars($selectedPaymentMethod['ultimo_cuatro']) : ''; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h6 class="mb-3">Resumen del pedido</h6>
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <div>
                                            <strong><?= htmlspecialchars($item['product']['nombre'] ?? ''); ?></strong>
                                            <div class="text-muted small">x<?= $item['quantity']; ?></div>
                                        </div>
                                        <strong>$<?= number_format($item['subtotal'], 2); ?></strong>
                                    </div>
                                <?php endforeach; ?>

                                <div class="d-flex justify-content-between mt-4 mb-2"><span>Subtotal</span><strong>$<?= number_format($total ?? 0, 2); ?></strong></div>
                                <div class="d-flex justify-content-between mb-3"><span>Envio</span><strong class="text-success">Gratis</strong></div>
                                <hr>
                                <div class="d-flex justify-content-between fs-4 fw-bold"><span>Total a pagar</span><span>$<?= number_format($total ?? 0, 2); ?></span></div>

                                <div class="mt-5 d-flex gap-3 flex-wrap">
                                    <a href="<?= site_url('checkout?step=2'); ?>" class="btn btn-outline-secondary">Volver</a>
                                    <form id="order-confirm-form" method="post" action="<?= site_url('pedido/confirmar'); ?>" class="flex-grow-1">
                                        <?= csrf_input(); ?>

                                        <?php if (!empty($selectedPaymentMethod) && isset($selectedPaymentMethod['id']) && (int) $selectedPaymentMethod['id'] === 0 && $selectedPaymentMethod['tipo'] === 'tarjeta'): ?>
                                            <input type="hidden" name="card_token" id="card_token" value="">
                                            <input type="hidden" name="card_brand" id="card_brand" value="">
                                            <input type="hidden" name="payment_method_id" id="payment_method_id" value="">

                                            <div class="section-card p-4 mb-4 border rounded-4">
                                                <h5 class="mb-3">Datos de la tarjeta</h5>
                                                <p class="text-muted small mb-3">Las tarjetas Visa y Mastercard se procesaran con OpenPay dentro de esta compra.</p>
                                                <div class="d-flex flex-wrap gap-2 mb-4">
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Tokenizacion segura</span>
                                                    <span class="badge bg-light text-dark border">Sin salir del checkout</span>
                                                    <span class="badge bg-light text-dark border">Cargo en MXN</span>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label for="cardholderName" class="form-label small fw-bold">Nombre del titular</label>
                                                        <input type="text" id="cardholderName" name="cardholder_name" class="form-control rounded-4" required maxlength="80" placeholder="Nombre tal como aparece en la tarjeta">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="cardNumber" class="form-label small fw-bold">Numero de tarjeta</label>
                                                        <input type="text" id="cardNumber" name="card_number" data-checkout="cardNumber" class="form-control rounded-4" required maxlength="19" placeholder="0000 0000 0000 0000">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="cardExpirationMonth" class="form-label small fw-bold">Mes</label>
                                                        <input type="text" id="cardExpirationMonth" name="card_expiration_month" data-checkout="cardExpirationMonth" class="form-control rounded-4" required maxlength="2" placeholder="MM">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="cardExpirationYear" class="form-label small fw-bold">Ano</label>
                                                        <input type="text" id="cardExpirationYear" name="card_expiration_year" data-checkout="cardExpirationYear" class="form-control rounded-4" required maxlength="2" placeholder="AA">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="securityCode" class="form-label small fw-bold">CVC</label>
                                                        <input type="text" id="securityCode" name="security_code" data-checkout="securityCode" class="form-control rounded-4" required maxlength="4" placeholder="CVC">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="cardholderEmail" class="form-label small fw-bold">Correo del titular</label>
                                                        <input type="email" id="cardholderEmail" name="cardholder_email" data-checkout="cardholderEmail" class="form-control rounded-4" required value="<?= htmlspecialchars($user['email'] ?? ''); ?>" placeholder="correo@dominio.com">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="docType" class="form-label small fw-bold">Tipo de documento</label>
                                                        <input type="text" id="docType" name="doc_type" data-checkout="docType" class="form-control rounded-4" value="DNI" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="docNumber" class="form-label small fw-bold">Documento</label>
                                                        <input type="text" id="docNumber" name="doc_number" data-checkout="docNumber" class="form-control rounded-4" required value="<?= htmlspecialchars(preg_replace('/\D+/', '', $user['telefono'] ?? '')); ?>" placeholder="Numero de documento">
                                                    </div>
                                                    <div class="col-12">
                                                        <div id="mp-card-error" class="alert alert-danger d-none"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <button type="submit" id="checkout-submit" class="btn btn-success btn-lg w-100 py-3">Confirmar y pagar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-5" data-animate="fade-up" data-animate-delay="120">
                        <div class="checkout-panel p-4 sticky-top summary-panel" style="top: 1.5rem;">
                            <h5 class="mb-3">Resumen del pedido</h5>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="small"><?= htmlspecialchars($item['product']['nombre'] ?? ''); ?><span class="text-muted"> x<?= $item['quantity']; ?></span></div>
                                    <strong class="small">$<?= number_format($item['subtotal'], 2); ?></strong>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between"><span class="text-muted">Total</span><span class="fs-5 fw-bold">$<?= number_format($total ?? 0, 2); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($openpayPublicKey) && !empty($openpayMerchantId)): ?>
<script src="https://openpay.s3.amazonaws.com/openpay.v1.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Openpay === 'undefined') {
            return;
        }

        Openpay.setId('<?= htmlspecialchars($openpayMerchantId); ?>');
        Openpay.setApiKey('<?= htmlspecialchars($openpayPublicKey); ?>');

        const form = document.getElementById('order-confirm-form');
        const submitButton = document.getElementById('checkout-submit');
        const cardTokenInput = document.getElementById('card_token');
        const cardBrandInput = document.getElementById('card_brand');
        const cardError = document.getElementById('mp-card-error');

        if (!form || !submitButton || !cardTokenInput) {
            return;
        }

        form.addEventListener('submit', function (event) {
            const shouldTokenize = !!document.querySelector('[name="card_number"]');
            if (!shouldTokenize) {
                return;
            }

            if (cardTokenInput.value.trim() !== '') {
                return;
            }

            event.preventDefault();
            submitButton.disabled = true;
            submitButton.textContent = 'Procesando pago...';
            cardError.classList.add('d-none');
            cardError.textContent = '';

            const cardData = {
                card_number: document.querySelector('[name="card_number"]').value.replace(/\s/g, ''),
                holder_name: document.querySelector('[name="cardholder_name"]').value,
                expiration_month: document.querySelector('[name="expiration_month"]').value,
                expiration_year: document.querySelector('[name="expiration_year"]').value,
                cvv2: document.querySelector('[name="cvv"]').value,
            };

            Openpay.token.create(cardData, function (response) {
                cardTokenInput.value = response.data.id || '';
                cardBrandInput.value = response.data.brand || '';
                form.submit();
            }, function (response) {
                let message = 'Error al generar el token de pago. Revisa los datos de tu tarjeta e intentalo de nuevo.';
                if (response && response.data && response.data.error_description) {
                    message = response.data.error_description;
                }

                submitButton.disabled = false;
                submitButton.textContent = 'Confirmar y pagar';
                cardError.textContent = message;
                cardError.classList.remove('d-none');
            });
        });
    });
</script>
<?php endif; ?>
