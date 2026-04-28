<?php
// 1. Simulación de datos o configuración (esto podría venir de una base de datos más adelante)
// El array de 15 artículos científicos
$articulos = [
        ["titulo" => "Biodisponibilidad de Compuestos Bioactivos", "imagen" => "https://images.unsplash.com/photo-1532187875605-2fe358a77e95?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo1.pdf"],
        ["titulo" => "Impacto de los Nootrópicos en el Foco Mental", "imagen" => "https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo2.pdf"],
        ["titulo" => "Estudio Clínico sobre Regeneración Celular", "imagen" => "https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo3.pdf"],
        ["titulo" => "Optimización del Metabolismo Mitocondrial", "imagen" => "https://images.unsplash.com/photo-1581093450021-4a7360e9a6ad?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo4.pdf"],
        ["titulo" => "Análisis de Pureza en Extractos Botánicos", "imagen" => "https://images.unsplash.com/photo-1582719471384-894fbb16e074?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo5.pdf"],
        ["titulo" => "Farmacocinética de Suplementos Premium", "imagen" => "https://images.unsplash.com/photo-1614935151651-0bea6508db6b?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo6.pdf"],
        ["titulo" => "Neurotransmisores y Rendimiento Cognitivo", "imagen" => "https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo7.pdf"],
        ["titulo" => "Efectos de la Microdosificación de Nutrientes", "imagen" => "https://images.unsplash.com/photo-1512069772995-ec65ed45afd6?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo8.pdf"],
        ["titulo" => "Respaldo Científico de Biomedics Souls", "imagen" => "https://images.unsplash.com/photo-1579154236594-e1797646a675?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo9.pdf"],
        ["titulo" => "Avances en la Salud Neurológica 2024", "imagen" => "https://images.unsplash.com/photo-1530210124550-912dc1381cb8?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo10.pdf"],
        ["titulo" => "Certificaciones de Pureza y Estabilidad", "imagen" => "https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo11.pdf"],
        ["titulo" => "Mecanismos de Absorción Celular", "imagen" => "https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo12.pdf"],
        ["titulo" => "Energía Celular y ATP: Reporte Técnico", "imagen" => "https://images.unsplash.com/photo-1532187643603-ba119ca4109e?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo13.pdf"],
        ["titulo" => "Eficacia de Ingredientes Bioactivos", "imagen" => "https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=800&q=80", "pdf" => "docs/articulo14.pdf"],
        ["titulo" => "Futuro de la Nutrición Biomédica", "imagen" => "https://images.unsplash.com/photo-1581093448792-3b20a67baf0b?auto=format&fit=crop&w=1200&q=80", "pdf" => "docs/articulo15.pdf"],
];
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Investigación y Avales · Biomedics Souls</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="styles.css" rel="stylesheet">

    <style>
        /* Estilos adicionales para la cuadrícula de PDFs */
        .pdf-icon-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc3545;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 5;
        }
        .magazine-card {
            transition: transform 0.3s ease;
            text-decoration: none !important;
        }
        .magazine-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="container py-5">

    <div class="row g-4 align-items-center mb-5">
        <div class="col-lg-8">
            <h1 class="display-4 fw-bold section-title">Ciencia con Alma</h1>
            <p class="lead text-muted">
                Nuestras fórmulas no son casualidad. Explora los estudios y la documentación técnica que respalda cada uno de nuestros suplementos.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($articulos as $index => $art): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= $art['pdf']; ?>" target="_blank" class="magazine-card d-block rounded-panel overflow-hidden h-100">

                    <div class="magazine-graphic position-relative" style="height: 240px;">
                        <img src="<?= $art['imagen']; ?>" alt="<?= $art['titulo']; ?>" class="w-100 h-100 object-fit-cover">

                        <div class="pdf-icon-badge">
                            <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                        </div>

                        <div class="magazine-overlay p-3">
                            <span class="badge bg-purple px-3">Estudio #<?= $index + 1; ?></span>
                        </div>
                    </div>

                    <div class="p-4 bg-white border-top">
                        <h5 class="fw-bold text-dark mb-3"><?= $art['titulo']; ?></h5>
                        <div class="d-flex align-items-center text-purple fw-semibold small">
                            LEER DOCUMENTO COMPLETO <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>