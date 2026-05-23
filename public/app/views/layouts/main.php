<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? csrf_token()); ?>">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Biomedics Souls'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset_url('css/styles.css'); ?>">
</head>
<body class="<?= isset($bodyClass) ? htmlspecialchars($bodyClass) : ''; ?>">
    <?php
    $cartFeedback = $_SESSION['cart_feedback'] ?? null;
    unset($_SESSION['cart_feedback']);
    ?>
    <?php require APPROOT . '/views/partials/header.php'; ?>

    <?php if (!empty($cartFeedback['message'])): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1085; margin-top: 5.5rem;">
            <div
                class="toast align-items-center border-0 shadow-lg"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
                data-cart-toast="true"
                data-bs-delay="3200"
            >
                <div class="d-flex">
                    <div class="toast-body">
                        <strong class="<?= ($cartFeedback['type'] ?? 'success') === 'error' ? 'text-danger' : 'text-success'; ?>">
                            <?= ($cartFeedback['type'] ?? 'success') === 'error' ? 'No se pudo agregar' : 'Agregado al carrito'; ?>
                        </strong>
                        <div class="small text-muted mt-1"><?= htmlspecialchars($cartFeedback['message']); ?></div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <main class="site-main">
        <?php if (isset($viewFile) && file_exists($viewFile)): ?>
            <?php require $viewFile; ?>
        <?php else: ?>
            <?= $content ?? ''; ?>
        <?php endif; ?>
    </main>

    <?php require APPROOT . '/views/partials/footer.php'; ?>
    <?php require APPROOT . '/views/partials/assistant-chat.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= asset_url('js/app.js'); ?>" defer></script>
</body>
</html>
