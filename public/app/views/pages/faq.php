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
                        ['¿Cómo puedo contactarme con ustedes?', 'Nos comunicaremos Principalmente a traves de whatsapppara brindarte una atención personalizada. Nuestro equipo de soporte está disponible para responder tus preguntas, ayudarte a elegir el producto adecuado y resolver cualquier inquietud que puedas tener sobre tu compra o nuestros productos.'],
                        ['¿Cómo elijo el suplemento adecuado para mi objetivo?', 'Identifica si buscas enfoque, recuperación o bienestar general. Revisa ingredientes y recomendaciones para seleccionar la fórmula que mejor se adapta a tu rutina.'],
                        ['¿Los productos son aptos para uso diario?', 'Sí, nuestras fórmulas están elaboradas para integración diaria siempre que se sigan las dosis recomendadas y, en caso de dudas médicas, consultes con tu profesional de salud.'],
                        ['¿Cómo se almacena el producto correctamente?', 'Conserva los suplementos en un lugar fresco y seco, lejos de la luz directa y fuentes de calor. Esto mantiene la potencia de los ingredientes durante más tiempo.'],
                        ['¿Qué diferencia a Biomedics Souls de otras marcas?', 'Nuestra propuesta combina investigación en biodisponibilidad, fórmulas limpias y una experiencia de uso premium. Cada producto prioriza evidencia y calidad clínica.'],
                        ['¿Puedo combinar diferentes suplementos de la línea?', 'Sí, nuestras fórmulas están diseñadas para complementar entre sí. Sin embargo, es recomendable revisar las etiquetas para evitar duplicación de ingredientes y consultar con un profesional si tienes condiciones médicas.'],
                        ['¿Cómo llegará mi pedido a mi domicilio?', 'Nos aliamos con FedEx para asegurar que tus productos lleguen en perfectas condiciones hasta la puerta de tu casa. Utilizamos sus servicios de logística para cubrir los tiempos de entrega prometidos y brindarte la tranquilidad de poder monitorear tu envío en todo momento con el número de guía que te proporcionaremos al procesar tu compra.'],
                        ['¿Hacen envios a todo el país?', 'Sí, realizamos envíos a todo el país. No importa dónde te encuentres, puedes disfrutar de nuestros productos con la comodidad de recibirlos directamente en tu domicilio.'],
                        ['¿Qué opciones de pago aceptan?', 'Aceptamos diversas formas de pago, incluyendo tarjetas de crédito y débito, así como transferencias bancarias. Puedes seleccionar la opción que mejor se adapte a tus necesidades durante el proceso de checkout.'],
                        ['¿Cuál es el tiempo de entrega estimado?', 'El tiempo de entrega puede variar según tu ubicación, pero generalmente se procesa dentro de 1-2 días hábiles y se entrega en 3-5 días hábiles después de ser enviado.']
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
