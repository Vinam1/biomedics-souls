<?php
/**
 * Vista de Ciencia e Investigación
 * Se carga dentro de views/layout/main.php
 */

$articulos = [
    ['titulo' => 'Biodisponibilidad de Compuestos Bioactivos', 'imagen' => 'https://images.unsplash.com/photo-1532187875605-2fe358a77e95?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo1.pdf'],
    ['titulo' => 'Impacto de los Nootrópicos en el Foco Mental', 'imagen' => 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo2.pdf'],
    ['titulo' => 'Estudio Clínico sobre Regeneración Celular', 'imagen' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo3.pdf'],
    ['titulo' => 'Optimización del Metabolismo Mitocondrial', 'imagen' => 'https://images.unsplash.com/photo-1581093450021-4a7360e9a6ad?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo4.pdf'],
    ['titulo' => 'Análisis de Pureza en Extractos Botánicos', 'imagen' => 'https://images.unsplash.com/photo-1582719471384-894fbb16e074?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo5.pdf'],
    ['titulo' => 'Farmacocinética de Suplementos Premium', 'imagen' => 'https://images.unsplash.com/photo-1614935151651-0bea6508db6b?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo6.pdf'],
    ['titulo' => 'Neurotransmisores y Rendimiento Cognitivo', 'imagen' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo7.pdf'],
    ['titulo' => 'Efectos de la Microdosificación de Nutrientes', 'imagen' => 'https://images.unsplash.com/photo-1512069772995-ec65ed45afd6?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo8.pdf'],
    ['titulo' => 'Respaldo Científico de Biomedics Souls', 'imagen' => 'https://images.unsplash.com/photo-1579154236594-e1797646a675?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo9.pdf'],
    ['titulo' => 'Avances en la Salud Neurológica 2024', 'imagen' => 'https://images.unsplash.com/photo-1530210124550-912dc1381cb8?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo10.pdf'],
    ['titulo' => 'Certificaciones de Pureza y Estabilidad', 'imagen' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo11.pdf'],
    ['titulo' => 'Mecanismos de Absorción Celular', 'imagen' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo12.pdf'],
    ['titulo' => 'Energía Celular y ATP: Reporte Técnico', 'imagen' => 'https://images.unsplash.com/photo-1532187643603-ba119ca4109e?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo13.pdf'],
    ['titulo' => 'Eficacia de Ingredientes Bioactivos', 'imagen' => 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=800&q=80', 'pdf' => 'docs/articulo14.pdf'],
    ['titulo' => 'Futuro de la Nutrición Biomédica', 'imagen' => 'https://images.unsplash.com/photo-1581093448792-3b20a67baf0b?auto=format&fit=crop&w=1200&q=80', 'pdf' => 'docs/articulo15.pdf'],
];
?>

<div class="container pb-5">
    <div class="row g-4 align-items-center mb-5 mt-2">
        <div class="col-lg-7">
            <h1 class="display-5 fw-bold mb-3 section-title">Ciencia e Investigación</h1>
            <p class="lead text-muted">
                En Biomedics Souls, la eficacia está respaldada por datos. Explora nuestra biblioteca de estudios clínicos, certificaciones y análisis técnicos.
            </p>
        </div>
        <div class="col-lg-5">
            <div class="section-surface p-4 shadow-sm border-0">
                <h5 class="fw-semibold mb-3" style="color: var(--primary);">Estándares Biomédicos</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bi bi-shield-check text-primary me-2 fs-5"></i>
                        <span>Grado de pureza farmacéutica</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bi bi-microscope text-primary me-2 fs-5"></i>
                        <span>Laboratorios certificados</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="bi bi-journal-medical text-primary me-2 fs-5"></i>
                        <span>Evidencia basada en resultados</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($articulos as $index => $art): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= asset_url($art['pdf']); ?>" target="_blank" class="magazine-card d-block h-100 text-decoration-none">
                    <div class="magazine-graphic position-relative" style="height: 240px;">
                        <img src="<?= $art['imagen']; ?>"
                             class="w-100 h-100 object-fit-cover"
                             alt="<?= htmlspecialchars($art['titulo']); ?>">

                        <div class="pdf-badge">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>

                        <div class="magazine-overlay p-3">
                            <span class="badge bg-purple-soft text-purple px-3 py-2 rounded-pill">
                                Documento #<?= str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 bg-white border-top">
                        <h3 class="h6 fw-bold text-dark mb-3 line-clamp-2"><?= htmlspecialchars($art['titulo']); ?></h3>
                        <div class="d-flex align-items-center small fw-bold" style="color: var(--primary);">
                            Abrir investigación <i class="bi bi-arrow-up-right-circle ms-2"></i>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    :root {
        --purple-soft: rgba(168, 85, 247, 0.15);
    }

    .bg-purple-soft {
        background-color: var(--purple-soft);
    }

    .text-purple {
        color: var(--secondary);
    }

    .pdf-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: white;
        color: #dc3545;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 10;
        transition: transform 0.2s ease;
    }

    .magazine-card:hover .pdf-badge {
        transform: scale(1.1) rotate(5deg);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .magazine-card {
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        border: 1px solid var(--border);
    }

    .magazine-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.12);
        border-color: var(--primary-soft);
    }
</style>
