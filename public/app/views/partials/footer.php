```php
<footer class="site-footer text-white pt-16 pb-12">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-4" data-animate="fade-up">
                    <span class="footer-brand-mark">
                        <img src="<?= asset_url('img/deco/logo.jpeg'); ?>" alt="Biomedics Souls" height="48" width="48">
                    </span>
                    <h5 class="fw-bold mb-0 text-white">Biomedics Souls</h5>
                </div>

                <p class="footer-copy mb-5">
                    Suplementos de grado cient&iacute;fico dise&ntilde;ados para optimizar tu cuerpo y mente.
                </p>

                <div class="d-flex gap-3" data-animate="fade-up" data-animate-delay="120">
                    <a href="https://www.facebook.com/share/1LbQax1gDe/" class="footer-social" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="footer-social" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="footer-social" aria-label="Chat">
                        <i class="bi bi-chat-dots"></i>
                    </a>
                    <a href="#" class="footer-social" aria-label="Correo">
                        <i class="bi bi-envelope"></i>
                    </a>
                </div>

                <!-- Logo Hecho en México -->
                <div class="mt-4" data-animate="fade-up" data-animate-delay="180">
                    <img
                        src="<?= asset_url('img/deco/Hecho_En_Mexico_2025.jpg'); ?>"
                        alt="Hecho en México"
                        class="img-fluid logo-hecho-mexico"
                    >
                </div>

            </div>

            <div class="col-md-3 col-lg-2" data-animate="fade-up" data-animate-delay="80">
                <h6 class="footer-title">Explorar</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= site_url('ciencia'); ?>">Art&iacute;culos</a></li>
                    <li><a href="<?= site_url('catalogo'); ?>">Cat&aacute;logo</a></li>
                    <li><a href="<?= site_url('faq'); ?>">Quiz</a></li>
                </ul>
            </div>

            <div class="col-md-3 col-lg-2" data-animate="fade-up" data-animate-delay="160">
                <h6 class="footer-title">Sobre Nosotros</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= site_url('home'); ?>">¿Qui&eacute;nes Somos?</a></li>
                    <li><a href="<?= site_url('faq'); ?>">Preguntas Frecuentes</a></li>
                    <li><a href="<?= site_url('contacto'); ?>">Contacto</a></li>
                </ul>
            </div>

            <div class="col-md-4 col-lg-4" data-animate="fade-up" data-animate-delay="240">
                <h6 class="footer-title mb-4">Prueba Nuestra App</h6>

                <p class="footer-copy small mb-4">
                    Descarga nuestra aplicaci&oacute;n m&oacute;vil y accede a protocolos, seguimiento y ofertas exclusivas.
                </p>

                <div class="footer-app-card text-center mx-auto" style="max-width: 220px;">
                    <div class="footer-qr-frame mb-3 shadow">
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=https://play.google.com/store/apps/details?id=com.biomedics.souls"
                            alt="QR Code App"
                            class="img-fluid footer-qr-image"
                            style="max-width: 130px;"
                        >
                    </div>

                    <p class="text-white-50 small fw-medium mb-0">
                        Escanea para descargar en Play Store
                    </p>
                </div>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between gap-3 mt-5 pt-4">
            <small class="text-white-50">
                &copy; <?= date('Y'); ?> Biomedics Souls. Todos los derechos reservados.
            </small>

            <div class="d-flex gap-3">
                <a href="<?= site_url('privacidad'); ?>" class="text-white-50 text-decoration-none">
                    Privacidad
                </a>
            </div>
        </div>
    </div>
</footer>