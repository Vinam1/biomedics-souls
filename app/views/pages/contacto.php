<?php
$title = 'Contáctanos | Biomedics Souls - Sensea';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3">Hablemos de <span class="text-primary">Salud y Soluciones</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 680px;">En Biomedcs Soul, priorizamos la atención personalizada. Elige la vía de comunicación que prefieras para recibir asesoría técnica o comercial.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-5 col-md-6">
            <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3"><i class="bi bi-whatsapp fs-1"></i></div>
                    <div>
                        <h4 class="fw-semibold">Atención Inmediata vía WhatsApp</h4>
                        <p class="text-muted">¿Necesitas una respuesta rápida? Inicia un chat con uno de nuestros especialistas para resolver dudas sobre productos o pedidos en tiempo real.</p>
                        <p class="text-muted">+52 (55) 1234567</p>
                    </div>
                </div>
                <a href="https://wa.me/525512345678" target="_blank" class="btn btn-success btn-lg w-100 rounded-4 py-3 fw-semibold">Iniciar Chat en WhatsApp →</a>
            </div>
        </div>

        <div class="col-lg-5 col-md-6">
            <div class="card border-0 shadow-sm h-100 p-4 rounded-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3"><i class="bi bi-envelope fs-1"></i></div>
                    <div>
                        <h4 class="fw-semibold">Consultas Formales por Correo</h4>
                        <p class="text-muted">Para cotizaciones detalladas, propuestas de consultoría o temas administrativos, escríbenos y nuestro equipo te contactará a la brevedad.</p>
                    </div>
                </div>
                <a href="mailto:contacto@biomedicssouls.com" class="btn btn-outline-primary btn-lg w-100 rounded-4 py-3 fw-semibold">contacto@biomedicssouls.com</a>
            </div>
        </div>
    </div>

    <div class="row g-5 mt-5 pt-5 border-top">
        <div class="col-md-4 text-center"><div class="mb-3"><i class="bi bi-geo-alt fs-1 text-muted"></i></div><h5 class="fw-semibold">Ubicación</h5><p class="text-muted mb-0">Ciudad de México, México</p></div>
        <div class="col-md-4 text-center"><div class="mb-3"><i class="bi bi-telephone fs-1 text-muted"></i></div><h5 class="fw-semibold">Teléfono</h5><p class="text-muted mb-0">+52 (55) 1234 5678</p></div>
        <div class="col-md-4 text-center"><div class="mb-3"><i class="bi bi-clock fs-1 text-muted"></i></div><h5 class="fw-semibold">Horario</h5><p class="text-muted mb-0">Lun - Vie: 9:00 - 18:00</p></div>
    </div>

    <div class="mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5 rounded-4">
                    <h3 class="text-center mb-4">¿Prefieres enviarnos un mensaje?</h3>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success rounded-4 text-center"><?= htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('contacto'); ?>">
                        <?= csrf_input(); ?>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nombre completo</label><input type="text" name="nombre" class="form-control rounded-4" required></div>
                            <div class="col-md-6"><label class="form-label">Correo electrónico</label><input type="email" name="email" class="form-control rounded-4" required></div>
                            <div class="col-12"><label class="form-label">Mensaje</label><textarea name="mensaje" class="form-control rounded-4" rows="5" required></textarea></div>
                            <div class="col-12 text-center"><button type="submit" class="btn btn-primary btn-lg px-5 rounded-4">Enviar Mensaje</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
