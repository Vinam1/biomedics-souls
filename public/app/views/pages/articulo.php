<?php
/**
 * articulo.php - Visor de documentos científicos
 */

// 1. Capturamos lo que viene por la URL
$archivo_param = isset($_GET['file']) ? $_GET['file'] : '';

// 2. Limpiamos la ruta
// Reemplazamos barras invertidas por normales para evitar líos en el servidor
$archivo_limpio = str_replace('\\', '/', $archivo_param);

// 3. Construimos la ruta física real para que PHP la verifique
// Usamos __DIR__ para que PHP sepa exactamente dónde está parado este archivo
$ruta_fisica = $_SERVER['DOCUMENT_ROOT'] . '/biomedics-souls/' . $archivo_limpio;

if (!empty($archivo_limpio) && file_exists($ruta_fisica)) {
    // 4. Construimos la URL que el navegador usará para mostrar el PDF
    $url_visor = "/biomedics-souls/" . $archivo_limpio;
} else {
    // Si falla, mostramos este error (puedes descomentar el echo de abajo para depurar)
    // echo "Debug: No se encontró en " . $ruta_fisica;
    echo "Lo sentimos, el informe técnico no está disponible en este momento.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visor de Evidencia | Biomedics Soul</title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background-color: #0f172a; }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>
    <!-- El visor con el PDF -->
    <iframe src="<?= $url_visor; ?>#toolbar=1"></iframe>
</body>
</html>