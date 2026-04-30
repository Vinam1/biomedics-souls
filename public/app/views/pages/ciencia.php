<?php
/**
 * Vista de Ciencia e Investigación
 * Se carga dentro de views/layout/main.php
 */

$articulos = [
    ['titulo' => 'Biodisponibilidad de Compuestos Bioactivos', 'imagen' => 'https://images.unsplash.com/photo-1532187875605-2fe358a77e95?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo1.pdf'],
    ['titulo' => 'Impacto de los Nootrópicos en el Foco Mental', 'imagen' => 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo2.pdf'],
    ['titulo' => 'Estudio Clínico sobre Regeneración Celular', 'imagen' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo3.pdf'],
    ['titulo' => 'Optimización del Metabolismo Mitocondrial', 'imagen' => 'https://images.unsplash.com/photo-1581093450021-4a7360e9a6ad?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo4.pdf'],
    ['titulo' => 'Análisis de Pureza en Extractos Botánicos', 'imagen' => 'https://images.unsplash.com/photo-1582719471384-894fbb16e074?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo5.pdf'],
    ['titulo' => 'Farmacocinética de Suplementos Premium', 'imagen' => 'https://images.unsplash.com/photo-1614935151651-0bea6508db6b?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo6.pdf'],
    ['titulo' => 'Neurotransmisores y Rendimiento Cognitivo', 'imagen' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo7.pdf'],
    ['titulo' => 'Efectos de la Microdosificación de Nutrientes', 'imagen' => 'https://images.unsplash.com/photo-1512069772995-ec65ed45afd6?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo8.pdf'],
    ['titulo' => 'Respaldo Científico de Biomedics Souls', 'imagen' => 'https://images.unsplash.com/photo-1579154236594-e1797646a675?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo9.pdf'],
    ['titulo' => 'Avances en la Salud Neurológica 2024', 'imagen' => 'https://images.unsplash.com/photo-1530210124550-912dc1381cb8?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo10.pdf'],
    ['titulo' => 'Certificaciones de Pureza y Estabilidad', 'imagen' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo11.pdf'],
    ['titulo' => 'Mecanismos de Absorción Celular', 'imagen' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo12.pdf'],
    ['titulo' => 'Energía Celular y ATP: Reporte Técnico', 'imagen' => 'https://images.unsplash.com/photo-1532187643603-ba119ca4109e?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo13.pdf'],
    ['titulo' => 'Eficacia de Ingredientes Bioactivos', 'imagen' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo14.pdf'],
    ['titulo' => 'Futuro de la Nutrición Biomédica', 'imagen' => 'https://images.unsplash.com/photo-1581093448792-3b20a67baf0b?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo15.pdf'],
];
?>

<section class="py-5 bg-navy text-white overflow-hidden">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-3 fw-semibold">Evidencia por producto</span>
                <h1 class="display-5 fw-bold mb-3">Ciencia, calidad y transparencia absoluta</h1>
                <p class="lead text-white-75 mb-4">Consulta los informes clínicos, análisis de pureza y reportes técnicos que respaldan cada una de nuestras fórmulas premium.</p>
                <div class="row g-3 text-white-75">
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">01</span>
                            <div>
                                <strong>Laboratorios certificados</strong>
                                <p class="mb-0">Pruebas independientes y trazabilidad total.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">02</span>
                            <div>
                                <strong>Documentos en PDF</strong>
                                <p class="mb-0">Descarga evidencia clara y directa.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">03</span>
                            <div>
                                <strong>Transparencia total</strong>
                                <p class="mb-0">Control de calidad en cada fórmula.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="badge bg-white text-dark rounded-circle p-3 shadow-sm">04</span>
                            <div>
                                <strong>Fórmulas validadas</strong>
                                <p class="mb-0">Sustancias bioactivas con respaldo científico.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="section-card bg-white border-0 shadow-lg p-5 text-dark h-100">
                    <h5 class="fw-semibold mb-4">Estándares Biomédicos</h5>
                    <p class="text-muted mb-4">Nuestra línea premium cumple con controles de calidad farmacéutica, análisis de terceros y protocolos de formulación científica.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                            <span>Pureza y estabilidad validada.</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                            <span>Ingredientes bioactivos con respaldo clínico.</span>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                            <span>Transparencia total en cada fórmula.</span>
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
                        <p class="text-muted mb-4 line-clamp-3">Accede al PDF con la investigación científica y evidencia técnica que justifica el uso de cada fórmula.</p>
                        <a href="<?= asset_url($art['pdf']); ?>" target="_blank" class="btn btn-primary mt-auto">Abrir PDF <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    .bg-navy {
        background: radial-gradient(circle at top left, rgba(124, 58, 237, 0.16), transparent 32%),
                    linear-gradient(180deg, #0f172a 0%, #071125 100%);
    }

    .article-card {
        border-radius: 2rem;
        overflow: hidden;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
    }

    .article-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 60px rgba(14, 27, 72, 0.16);
    }

    .article-card-figure {
        min-height: 240px;
    }

    .article-card-figure img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .article-card:hover .article-card-figure img {
        transform: scale(1.05);
    }

    .pdf-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #d63384;
        font-size: 1.2rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        transition: transform 0.2s ease;
    }

    .article-card:hover .pdf-badge {
        transform: scale(1.08);
    }

    .article-card-label {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        backdrop-filter: blur(10px);
    }

    .line-clamp-2,
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        -webkit-line-clamp: 2;
    }

    .line-clamp-3 {
        -webkit-line-clamp: 3;
    }

    .bg-opacity-10 {
        background-color: rgba(59, 130, 246, 0.08) !important;
    }
</style>
