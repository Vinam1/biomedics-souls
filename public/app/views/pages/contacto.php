<?php
$title = 'Contáctanos | Biomedics Souls - Sensea';
?>

<section class="page-shell py-5">
    <div class="container">
        <div class="page-hero mb-5" data-animate="fade-up">
            <span class="eyebrow-pill">Atención personalizada</span>
            <h1 class="page-title mb-3">Hablemos de <span class="text-gradient">salud y soluciones</span></h1>
            <p class="page-subtitle mx-auto">Elige la vía de comunicación que prefieras para recibir asesoría técnica, comercial o acompañamiento sobre tus productos.</p>
        </div>

        <div class="row g-4 justify-content-center mb-5">
            <div class="col-lg-5 col-md-6" data-animate="fade-up">
                <div class="contact-card h-100 p-4">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="contact-icon whatsapp"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <h4 class="fw-semibold">Atención inmediata vía WhatsApp</h4>
                            <p class="text-muted">Inicia un chat con uno de nuestros especialistas para resolver dudas sobre productos o pedidos en tiempo real.</p>
                            <p class="text-muted mb-0">+52 (56) 4796 9316</p>
                        </div>
                    </div>
                    <a href="https://wa.me/525647969316" target="_blank" class="btn btn-success btn-lg w-100 rounded-4 py-3 fw-semibold">Iniciar chat en WhatsApp <i class="bi bi-arrow-right ms-2"></i></a>
                </div>
            </div>

            <div class="col-lg-5 col-md-6" data-animate="fade-up" data-animate-delay="120">
                <div class="contact-card h-100 p-4">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="contact-icon email"><i class="bi bi-envelope-paper"></i></div>
                        <div>
                            <h4 class="fw-semibold">Consultas formales por correo</h4>
                            <p class="text-muted">Para cotizaciones detalladas, propuestas de consultoría o temas administrativos, escríbenos y nuestro equipo te contactará a la brevedad.</p>
                        </div>
                    </div>
                    <a href="mailto:contacto@biomedicssouls.com" class="btn btn-outline-primary btn-lg w-100 rounded-4 py-3 fw-semibold">soulsbiomedics@gmail.com</a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4" data-animate="fade-up">
                <div class="info-tile text-center h-100">
                    <i class="bi bi-geo-alt"></i>
                    <h5 class="fw-semibold">Ubicación</h5>
                    <p class="text-muted mb-0">Av. Paseo del Titanio 42-B Joyas de Cuautitlán, Cuautitlán, México</p>
                </div>
            </div>
            <div class="col-md-4" data-animate="fade-up" data-animate-delay="100">
                <div class="info-tile text-center h-100">
                    <i class="bi bi-telephone"></i>
                    <h5 class="fw-semibold">Teléfono</h5>
                    <p class="text-muted mb-0">+52 (56) 4796 9316</p>
                </div>
            </div>
            <div class="col-md-4" data-animate="fade-up" data-animate-delay="200">
                <div class="info-tile text-center h-100">
                    <i class="bi bi-clock-history"></i>
                    <h5 class="fw-semibold">Horario</h5>
                    <p class="text-muted mb-0">Lun - Vie: 9:00 - 18:00</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8" data-animate="fade-up">
                <div class="section-card p-4 p-lg-5">
                    <h3 class="text-center mb-4">¿Prefieres enviarnos un mensaje?</h3>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success rounded-4 text-center"><?= htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('contacto'); ?>">
                        <?= csrf_input(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" name="nombre" class="form-control rounded-4" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email" class="form-control rounded-4" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mensaje</label>
                                <textarea name="mensaje" class="form-control rounded-4" rows="5" required></textarea>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-4">Enviar mensaje</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
