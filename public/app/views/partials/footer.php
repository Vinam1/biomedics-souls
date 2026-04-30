<footer class="site-footer text-light py-5">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="footer-brand-mark">
                        <img src="<?= asset_url('img/deco/logo.jpeg'); ?>" alt="Biomedics Souls" height="42" width="42">
                    </span>
                    <h5 class="fw-bold mb-0 text-white">Biomedics Souls</h5>
                </div>
                <p class="footer-copy mb-4">
                    Suplementos de grado cient&iacute;fico dise&ntilde;ados para optimizar tu cuerpo y mente.
                </p>
                <div class="d-flex gap-3">
                    <a href="https://www.facebook.com/share/1LbQax1gDe/" class="footer-social" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="footer-social" aria-label="Chat"><i class="bi bi-chat-dots"></i></a>
                    <a href="#" class="footer-social" aria-label="Correo"><i class="bi bi-envelope"></i></a>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <h6 class="footer-title">Ciencia</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= site_url('ciencia'); ?>">Investigaci&oacute;n</a></li>
                    <li><a href="<?= site_url('catalogo'); ?>">Ingredientes</a></li>
                    <li><a href="<?= site_url('faq'); ?>">Certificaciones</a></li>
                </ul>
            </div>

            <div class="col-md-4 col-lg-3">
                <h6 class="footer-title">Compa&ntilde;&iacute;a</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= site_url('home'); ?>">Sobre Nosotros</a></li>
                    <li><a href="<?= site_url('faq'); ?>">Preguntas Frecuentes</a></li>
                    <li><a href="<?= site_url('contacto'); ?>">Contacto</a></li>
                </ul>
            </div>

            <div class="col-md-4 col-lg-2">
                <h6 class="footer-title">Suscripci&oacute;n</h6>
                <p class="footer-copy small mb-3">&Uacute;nete para recibir noticias y ofertas exclusivas.</p>
                <form class="footer-subscribe" action="<?= site_url('contacto'); ?>" method="get">
                    <input type="email" class="form-control" placeholder="Tu correo electr&oacute;nico" aria-label="Tu correo electr&oacute;nico">
                    <button type="submit" class="btn btn-brand" aria-label="Enviar">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between gap-3 mt-5 pt-4">
            <small class="text-white-50">&copy; <?= date('Y'); ?> Biomedics Souls. Todos los derechos reservados.</small>
            <div class="d-flex gap-3">
                <a href="#" class="text-white-50 text-decoration-none">Privacidad</a>
                <a href="#" class="text-white-50 text-decoration-none">T&eacute;rminos</a>
            </div>
        </div>
    </div>
</footer>
