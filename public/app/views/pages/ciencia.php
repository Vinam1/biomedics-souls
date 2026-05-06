<?php
/**
 * Vista de Ciencia e Investigación
 * Se carga dentro de views/layout/main.php
 */

// He corregido las rutas para que sean compatibles con cualquier servidor (Linux/Windows)
$articulos = [
    ['titulo' => 'Gel Centella Asiatica', 'imagen' => 'https://images.unsplash.com/photo-1532187875605-2fe358a77e95?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/gel-centella-asiatica.pdf'],
    ['titulo' => 'Serum Facial', 'imagen' => 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/serum-facial.pdf'],
    ['titulo' => 'Inulina de Achicoria', 'imagen' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/Inulina-de-achicoria.pdf'],
    ['titulo' => 'Colageno', 'imagen' => 'https://images.unsplash.com/photo-1581093450021-4a7360e9a6ad?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/colageno.pdf'],
    ['titulo' => 'Vitamina B3', 'imagen' => 'https://images.unsplash.com/photo-1582719471384-894fbb16e074?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/NAD.pdf'],
    ['titulo' => 'Vitamina C', 'imagen' => 'https://images.unsplash.com/photo-1614935151651-0bea6508db6b?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/vitaminaC.pdf'],
    ['titulo' => 'Pro-Flora', 'imagen' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/pro-flora.pdf'],
    ['titulo' => 'ashwagandha', 'imagen' => 'https://images.unsplash.com/photo-1512069772995-ec65ed45afd6?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/ashwagandha.pdf'],
    ['titulo' => 'VITEX', 'imagen' => 'https://images.unsplash.com/photo-1579154236594-e1797646a675?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/vitex.pdf'],
    ['titulo' => 'Astaxantina', 'imagen' => 'https://images.unsplash.com/photo-1530210124550-912dc1381cb8?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/astaxantina.pdf'],
    ['titulo' => 'CBF', 'imagen' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/cbf.pdf'],
    ['titulo' => 'Resveratrol', 'imagen' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/resveratrol.pdf'],
    ['titulo' => 'ALA', 'imagen' => 'https://images.unsplash.com/photo-1532187643603-ba119ca4109e?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/ala.pdf'],
    ['titulo' => 'Glucosamina', 'imagen' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/glucosamina.pdf'],
    ['titulo' => 'Glisanato de Magnesio', 'imagen' => 'https://images.unsplash.com/photo-1581093448792-3b20a67baf0b?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'public/assets/articulos/glisanato-de-magnesio.pdf'],
];
?>

<section class="py-5 bg-navy text-white overflow-hidden">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-3 fw-semibold">Evidencia por producto</span>
                <h1 class="display-5 fw-bold mb-3">Ciencia, calidad y transparencia absoluta</h1>
                <p class="lead text-white-75 mb-4">Para tu tranquilidad, ponemos a tu alcance la documentación técnica y los estudios de pureza que respaldan la excelencia de nuestros productos. Como tu consultoría de salud de confianza, en <strong>Biomedics Soul</strong> unimos la ciencia de vanguardia con un trato humano para ofrecerte soluciones de bienestar a tu medida.</p>
                <div class="row g-3 text-white-75">
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">01</span>
                            <div>
                                <strong>Laboratorios certificados</strong>
                                <p class="mb-0 text-sm">Pruebas independientes y trazabilidad total.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">02</span>
                            <div>
                                <strong>Documentos PDF</strong>
                                <p class="mb-0 text-sm">Visualización y descarga de evidencia clara.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">03</span>
                            <div>
                                <strong>Transparencia total</strong>
                                <p class="mb-0 text-sm">Control estricto en cada una de nuestras fórmulas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">04</span>
                            <div>
                                <strong>Fórmulas validadas</strong>
                                <p class="mb-0 text-sm">Sustancias con alto respaldo clínico.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="section-card bg-white border-0 shadow-lg p-5 text-dark h-100" style="border-radius: 2rem;">
                    <h5 class="fw-semibold mb-4 text-primary">Estándares Biomédicos</h5>
                    <p class="text-muted mb-4 small">Nuestra línea premium cumple con controles de calidad farmacéutica, análisis de terceros y protocolos de formulación científica rigurosos.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                            <span class="small fw-medium">Pureza y estabilidad validada.</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                            <span class="small fw-medium">Ingredientes bioactivos con respaldo clínico.</span>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                            <span class="small fw-medium">Transparencia total en cada lote.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <?php foreach ($articulos as $index => $art): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card article-card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="position-relative article-card-figure overflow-hidden">
                        <img src="<?= $art['imagen']; ?>" class="card-img-top" alt="<?= htmlspecialchars($art['titulo']); ?>">
                        <div class="pdf-badge">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>
                        <div class="article-card-label badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">Documento #<?= str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 fw-bold mb-3 line-clamp-2"><?= htmlspecialchars($art['titulo']); ?></h3>
                        <p class="text-muted mb-4 line-clamp-3 small">Accede a la investigación técnica y evidencia científica que fundamenta la eficacia de esta formulación.</p>
                        
                        <!-- CORRECCIÓN CLAVE: El link ahora apunta a articulo.php pasando el archivo por parámetro -->
                        <a href="app/views/pages/articulo.php?file=<?= urlencode($art['pdf']); ?>" class="btn btn-primary mt-auto" target="_blank">
                            Abrir Informe <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    /* Se mantienen tus estilos base con ligeros ajustes de legibilidad */
    .bg-navy {
        background: radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 35%),
                    linear-gradient(180deg, #0f172a 0%, #071125 100%);
    }

    .article-card {
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .article-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
    }

    .article-card-figure { height: 220px; }
    .article-card-figure img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .article-card:hover .article-card-figure img { transform: scale(1.1); }

    .pdf-badge {
        position: absolute; top: 1rem; right: 1rem; width: 40px; height: 40px;
        border-radius: 50%; background: white; display: flex; align-items: center;
        justify-content: center; color: #ef4444; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .text-sm { font-size: 0.875rem; }
</style>