<?php
$title = 'Cont&aacute;ctanos | Biomedics Souls - Sensea';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3">Hablemos de <span class="text-primary">Salud y Soluciones</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 680px;">En Biomedics Soul, priorizamos la atenci&oacute;n personalizada. Elige la v&iacute;a de comunicaci&oacute;n que prefieras para recibir asesor&iacute;a t&eacute;cnica o comercial.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-5 col-md-6">
            <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3"><i class="bi bi-whatsapp fs-1"></i></div>
                    <div>
                        <h4 class="fw-semibold">Atenci&oacute;n inmediata v&iacute;a WhatsApp</h4>
                        <p class="text-muted">&iquest;Necesitas una respuesta r&aacute;pida? Inicia un chat con uno de nuestros especialistas para resolver dudas sobre productos o pedidos en tiempo real.</p>
                        <p class="text-muted">+52 (56) 4796 9316</p>
                    </div>
                </div>
                <a href="https://wa.me/525647969316" target="_blank" class="btn btn-success btn-lg w-100 rounded-4 py-3 fw-semibold">Iniciar chat en WhatsApp <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>

        <div class="col-lg-5 col-md-6">
            <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3"><i class="bi bi-envelope-paper fs-1"></i></div>
                    <div>
                        <h4 class="fw-semibold">Consultas formales por correo</h4>
                        <p class="text-muted">Para cotizaciones detalladas, propuestas de consultor&iacute;a o temas administrativos, escr&iacute;benos y nuestro equipo te contactar&aacute; a la brevedad.</p>
                    </div>
                </div>
                <a href="mailto:contacto@biomedicssouls.com" class="btn btn-outline-primary btn-lg w-100 rounded-4 py-3 fw-semibold">soulsbiomedics@gmail.com</a>
            </div>
        </div>
    </div>

    <div class="row g-5 mt-5 pt-5 border-top">
        <div class="col-md-4 text-center"><div class="mb-3"><i class="bi bi-geo-alt fs-1 text-muted"></i></div><h5 class="fw-semibold">Ubicaci&oacute;n</h5><p class="text-muted mb-0">Av. Paseo del Titanio 42-B Joyas de Cuautitl&aacute;n, Cuautitl&aacute;n, M&eacute;xico</p></div>
        <div class="col-md-4 text-center"><div class="mb-3"><i class="bi bi-telephone fs-1 text-muted"></i></div><h5 class="fw-semibold">Tel&eacute;fono</h5><p class="text-muted mb-0">+52 (56) 4796 9316</p></div>
        <div class="col-md-4 text-center"><div class="mb-3"><i class="bi bi-clock-history fs-1 text-muted"></i></div><h5 class="fw-semibold">Horario</h5><p class="text-muted mb-0">Lun - Vie: 9:00 - 18:00</p></div>
    </div>

    <div class="mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5 rounded-4">
                    <h3 class="text-center mb-4">&iquest;Prefieres enviarnos un mensaje?</h3>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success rounded-4 text-center"><?= htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('contacto'); ?>">
                        <?= csrf_input(); ?>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nombre completo</label><input type="text" name="nombre" class="form-control rounded-4" required></div>
                            <div class="col-md-6"><label class="form-label">Correo electr&oacute;nico</label><input type="email" name="email" class="form-control rounded-4" required></div>
                            <div class="col-12"><label class="form-label">Mensaje</label><textarea name="mensaje" class="form-control rounded-4" rows="5" required></textarea></div>
                            <div class="col-12 text-center"><button type="submit" class="btn btn-primary btn-lg px-5 rounded-4">Enviar mensaje</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
