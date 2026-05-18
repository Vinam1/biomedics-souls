<section class="page-shell py-5">
    <div class="container">
        <div class="page-hero page-hero-compact mb-5" data-animate="fade-up">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="eyebrow-pill">Respuestas claras</span>
                    <h1 class="page-title mb-3">Preguntas frecuentes para comprar con <span class="text-gradient">confianza</span></h1>
                    <p class="page-subtitle mb-0">Te ayudamos a entender mejor nuestras fórmulas, su uso y lo que diferencia a Biomedics Souls dentro del mercado.</p>
                </div>
                <div class="col-lg-4" data-animate="zoom-in">
                    <div class="faq-aside-card">
                        <div class="icon-chip text-purple mb-3"><i class="bi bi-headset"></i></div>
                        <h5 class="mb-3">¿Necesitas ayuda rápida?</h5>
                        <p class="text-muted mb-4">Visita la página de contacto para conectar por WhatsApp o correo y recibir atención personalizada.</p>
                        <a href="<?= site_url('contacto'); ?>" class="btn btn-primary w-100">Ir a Contacto</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="accordion faq-accordion" id="faqAccordion">
                    <?php
                    $faqs = [
                        ['¿Cómo elijo el suplemento adecuado para mi objetivo?', 'Identifica si buscas enfoque, recuperación o bienestar general. Revisa ingredientes y recomendaciones para seleccionar la fórmula que mejor se adapta a tu rutina.'],
                        ['¿Los productos son aptos para uso diario?', 'Sí, nuestras fórmulas están elaboradas para integración diaria siempre que se sigan las dosis recomendadas y, en caso de dudas médicas, consultes con tu profesional de salud.'],
                        ['¿Cómo se almacena el producto correctamente?', 'Conserva los suplementos en un lugar fresco y seco, lejos de la luz directa y fuentes de calor. Esto mantiene la potencia de los ingredientes durante más tiempo.'],
                        ['¿Qué diferencia a Biomedics Souls de otras marcas?', 'Nuestra propuesta combina investigación en biodisponibilidad, fórmulas limpias y una experiencia de uso premium. Cada producto prioriza evidencia y calidad clínica.'],
                    ];
                    ?>
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item faq-item mb-3" data-animate="fade-up" data-animate-delay="<?= $index * 90; ?>">
                            <h2 class="accordion-header" id="faq-heading-<?= $index + 1; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-collapse-<?= $index + 1; ?>" aria-expanded="false" aria-controls="faq-collapse-<?= $index + 1; ?>">
                                    <?= htmlspecialchars($faq[0]); ?>
                                </button>
                            </h2>
                            <div id="faq-collapse-<?= $index + 1; ?>" class="accordion-collapse collapse" aria-labelledby="faq-heading-<?= $index + 1; ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    <?= htmlspecialchars($faq[1]); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
