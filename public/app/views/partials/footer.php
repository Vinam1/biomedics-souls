<!-- views/partials/footer.php -->
<footer class="text-light py-5" style="background: #0f172a;">
    <div class="container">
        <div class="row g-5">

            <!-- Columna 1: Logo y descripciÃ³n -->
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="<?= asset_url('img/deco/logo.jpeg'); ?>"
                         alt="Biomedics Souls"
                         height="45">
                    <div>
                        <h5 class="fw-bold mb-0 text-white">Biomedics Souls</h5>
                        <small class="text-muted">Sensea</small>
                    </div>
                </div>
                <p class="text-light opacity-75" style="max-width: 320px;">
                    Suplementos de grado cientÃ­fico diseÃ±ados para optimizar tu cuerpo y mente.
                </p>

                <!-- Redes sociales -->
                <div class="d-flex gap-3 mt-4">
                    <a href="https://www.facebook.com/share/1LbQax1gDe/" class="text-light opacity-75 hover-opacity-100">
                        <i class="bi bi-facebook fs-4"></i>
                    </a>
                    <a href="#" class="text-light opacity-75 hover-opacity-100">
                        <i class="bi bi-instagram fs-4"></i>
                    </a>
                    <a href="#" class="text-light opacity-75 hover-opacity-100">
                        <i class="bi bi-chat-dots fs-4"></i>
                    </a>
                    <a href="#" class="text-light opacity-75 hover-opacity-100">
                        <i class="bi bi-envelope fs-4"></i>
                    </a>
                </div>
            </div>

            <!-- Columna 2: Ciencia -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-semibold text-white mb-3">Ciencia</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= site_url('ciencia'); ?>" class="text-light opacity-75 text-decoration-none">InvestigaciÃ³n</a></li>
                    <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Ingredientes</a></li>
                    <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Certificaciones</a></li>
                </ul>
            </div>

            <!-- Columna 3: CompaÃ±Ã­a -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-semibold text-white mb-3">CompaÃ±Ã­a</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Sobre Nosotros</a></li>
                    <li class="mb-2"><a href="<?= site_url('faq'); ?>" class="text-light opacity-75 text-decoration-none">Preguntas Frecuentes</a></li>
                    <li class="mb-2"><a href="<?= site_url('contacto'); ?>" class="text-light opacity-75 text-decoration-none">Contacto</a></li>
                </ul>
            </div>

            <!-- Columna 4: Contacto y Legal -->
            <div class="col-lg-4 col-md-12 text-lg-end">
                <div class="mb-4">
                    <h6 class="fw-semibold text-white mb-3">ContÃ¡ctanos</h6>
                    <p class="text-light opacity-75 mb-1">+52 (56) 4796 9316</p>
                    <p class="text-light opacity-75">soulsbiomedics@gmail.com</p>
                </div>

                <div class="mt-5 pt-4 border-top border-secondary">
                    <small class="text-light opacity-50">
                        Â© <?= date('Y'); ?> Biomedics Souls. Todos los derechos reservados.
                    </small>
                </div>

                <div class="mt-3">
                    <a href="#" class="text-light opacity-50 text-decoration-none me-3">Privacidad</a>
                    <a href="#" class="text-light opacity-50 text-decoration-none">TÃ©rminos</a>
                </div>
            </div>
        </div>
    </div>
</footer>