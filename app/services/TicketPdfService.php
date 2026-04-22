<?php

class TicketPdfService
{
    public static function render(array $order, array $items, ?array $transaction = null): string
    {
        $lines = self::buildLines($order, $items, $transaction);
        $width = 226;
        $height = max(320, 40 + (count($lines) * 13));
        $startY = $height - 24;

        $content = "BT\n/F1 9 Tf\n11 TL\n12 " . $startY . " Td\n";
        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $content .= "T*\n";
            }
            $content .= '(' . self::escapePdfText($line) . ") Tj\n";
        }
        $content .= "ET";

        return self::buildPdfDocument($content, $width, $height);
    }

    private static function buildLines(array $order, array $items, ?array $transaction): array
    {
        $customerName = trim(($order['cliente_nombre'] ?? '') . ' ' . ($order['cliente_apellidos'] ?? ''));
        $status = ucfirst(str_replace('_', ' ', (string) ($order['estado_pedido'] ?? 'pendiente')));
        $method = ucfirst(str_replace('_', ' ', (string) ($order['tipo_metodo_pago'] ?? 'metodo guardado')));
        $masked = !empty($order['ultimo_cuatro']) ? ' ****' . $order['ultimo_cuatro'] : '';
        $lines = [
            'BIOMEDICS SOULS',
            'Ticket de compra',
            str_repeat('-', 32),
            'Pedido: ' . ($order['numero_pedido'] ?? ''),
            'Fecha: ' . date('d/m/Y H:i', strtotime((string) ($order['created_at'] ?? 'now'))),
            'Estado: ' . $status,
            'Cliente: ' . ($customerName !== '' ? $customerName : 'Cliente'),
            'Email: ' . ($order['cliente_email'] ?? ''),
            'Pago: ' . trim($method . $masked),
        ];

        if ($transaction) {
            $lines[] = 'Ref: ' . ($transaction['referencia'] ?? '');
            $lines[] = 'Gateway: ' . ($transaction['gateway'] ?? '');
        }

        $lines[] = str_repeat('-', 32);

        foreach ($items as $item) {
            foreach (self::wrapText((string) ($item['producto_nombre'] ?? ''), 28) as $wrapped) {
                $lines[] = $wrapped;
            }
            $lines[] = (int) ($item['cantidad'] ?? 0) . ' x $' . number_format((float) ($item['precio_unitario'] ?? 0), 2) . '   $' . number_format((float) ($item['subtotal'] ?? 0), 2);
        }

        $lines[] = str_repeat('-', 32);
        $lines[] = 'Subtotal: $' . number_format((float) ($order['subtotal'] ?? 0), 2);
        $lines[] = 'Envio: $' . number_format((float) ($order['costo_envio'] ?? 0), 2);
        $lines[] = 'TOTAL: $' . number_format((float) ($order['total'] ?? 0), 2);
        $lines[] = str_repeat('-', 32);
        $lines[] = 'Enviar a:';

        $addressLines = [
            trim((string) ($order['direccion_calle'] ?? '') . ' #' . (string) ($order['direccion_numero_exterior'] ?? '')),
            trim((string) ($order['direccion_colonia'] ?? '') . ', ' . (string) ($order['direccion_ciudad'] ?? '')),
            trim((string) ($order['direccion_estado'] ?? '') . ' ' . (string) ($order['direccion_codigo_postal'] ?? '')),
            (string) ($order['direccion_pais'] ?? ''),
        ];

        foreach ($addressLines as $addressLine) {
            if ($addressLine === '' || $addressLine === '#') {
                continue;
            }
            foreach (self::wrapText($addressLine, 32) as $wrapped) {
                $lines[] = $wrapped;
            }
        }

        if (!empty($order['direccion_telefono'])) {
            $lines[] = 'Tel: ' . $order['direccion_telefono'];
        }

        $lines[] = str_repeat('-', 32);
        $lines[] = 'Gracias por tu compra.';

        return $lines;
    }

    private static function wrapText(string $text, int $maxChars): array
    {
        $text = trim((string) preg_replace('/\s+/', ' ', $text));
        if ($text === '') {
            return [];
        }

        return explode("\n", wordwrap($text, $maxChars, "\n", true));
    }

    private static function escapePdfText(string $text): string
    {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: $text;
        $encoded = str_replace('\\', '\\\\', $encoded);
        $encoded = str_replace('(', '\\(', $encoded);
        $encoded = str_replace(')', '\\)', $encoded);
        return $encoded;
    }

    private static function buildPdfDocument(string $content, int $width, int $height): string
    {
        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $width . ' ' . $height . '] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
        $objects[] = '5 0 obj << /Length ' . strlen($content) . " >> stream\n" . $content . "\nendstream endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= '%%EOF';

        return $pdf;
    }
}
