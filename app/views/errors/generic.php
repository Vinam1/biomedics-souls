<div class="container py-5">
    <div class="mx-auto text-center" style="max-width: 640px;">
        <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($title ?? 'Ha ocurrido un error'); ?></h1>
        <p class="text-muted mb-0"><?= htmlspecialchars($message ?? 'No pudimos completar la solicitud.'); ?></p>
    </div>
</div>
