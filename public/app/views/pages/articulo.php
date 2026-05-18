<?php
$archivo_param = isset($_GET['file']) ? $_GET['file'] : '';
$archivo_limpio = str_replace('\\', '/', $archivo_param);
$ruta_fisica = $_SERVER['DOCUMENT_ROOT'] . '/biomedics-souls/' . $archivo_limpio;

if (!empty($archivo_limpio) && file_exists($ruta_fisica)) {
    $url_visor = '/biomedics-souls/' . $archivo_limpio;
} else {
    echo 'Lo sentimos, el informe técnico no está disponible en este momento.';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visor de Evidencia | Biomedics Souls</title>
    <style>
        :root {
            color-scheme: dark;
            --frame-bg: #0f172a;
            --frame-panel: rgba(15, 23, 42, 0.88);
            --frame-border: rgba(255, 255, 255, 0.08);
            --frame-accent: #7dd3fc;
            --frame-brand: #7e22ce;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(125, 211, 252, 0.18), transparent 30%),
                radial-gradient(circle at bottom left, rgba(126, 34, 206, 0.18), transparent 30%),
                var(--frame-bg);
            font-family: Manrope, system-ui, sans-serif;
            color: #fff;
        }

        .viewer-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            gap: 1rem;
        }

        .viewer-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 1.25rem;
            background: var(--frame-panel);
            border: 1px solid var(--frame-border);
            backdrop-filter: blur(14px);
        }

        .viewer-toolbar h1 {
            margin: 0;
            font-size: 1rem;
        }

        .viewer-toolbar p {
            margin: 0.25rem 0 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.92rem;
        }

        .viewer-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.9rem 1.1rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--frame-brand), #5b21b6);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 16px 30px rgba(126, 34, 206, 0.24);
        }

        .viewer-frame {
            flex: 1;
            min-height: 0;
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid var(--frame-border);
            background: rgba(255, 255, 255, 0.03);
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.28);
        }

        iframe {
            width: 100%;
            height: 100%;
            min-height: calc(100vh - 8rem);
            border: none;
        }

        @media (max-width: 768px) {
            .viewer-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            iframe {
                min-height: calc(100vh - 10rem);
            }
        }
    </style>
</head>
<body>
    <div class="viewer-shell">
        <div class="viewer-toolbar">
            <div>
                <h1>Visor de evidencia científica</h1>
                <p>Consulta el documento técnico en pantalla completa o ábrelo directamente en otra pestaña.</p>
            </div>
            <div class="viewer-actions">
                <a href="<?= htmlspecialchars($url_visor); ?>" target="_blank" rel="noopener noreferrer">Abrir PDF</a>
            </div>
        </div>

        <div class="viewer-frame">
            <iframe src="<?= htmlspecialchars($url_visor); ?>#toolbar=1"></iframe>
        </div>
    </div>
</body>
</html>
