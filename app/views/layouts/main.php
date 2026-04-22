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
<body>
    <?php require APPROOT . '/views/partials/header.php'; ?>

    <main class="py-4">
        <?php if (isset($viewFile) && file_exists($viewFile)): ?>
            <?php require $viewFile; ?>
        <?php else: ?>
            <?= $content ?? ''; ?>
        <?php endif; ?>
    </main>

    <?php require APPROOT . '/views/partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= asset_url('js/app.js'); ?>" defer></script>
</body>
</html>
