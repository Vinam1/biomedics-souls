<?php

class GeminiChatService
{
    private string $apiKey;
    private string $model;
    private string $endpoint;

    public function __construct()
    {
        $this->apiKey = trim((string) (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : ''));
        $this->model = trim((string) (defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-2.5-flash'));
        $this->endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . rawurlencode($this->model)
            . ':generateContent';
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    public function generateChatResult(string $message, array $products, array $history = []): array
    {
        return [
            'reply' => $this->generateReply($message, $products, $history),
            'suggestions' => $this->buildFollowUpSuggestions($message, $products),
        ];
    }

    public function generateReply(string $message, array $products, array $history = []): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('GEMINI_API_KEY no esta configurada en el entorno.');
        }

        $catalogContext = $this->buildCatalogContext($products);
        $contents = [];

        foreach (array_slice($history, -6) as $turn) {
            $role = ($turn['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $text = trim((string) ($turn['text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $text],
                ],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [
                [
                    'text' => $this->buildPrompt($message, $catalogContext),
                ],
            ],
        ];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.45,
                'topP' => 0.9,
                'maxOutputTokens' => 800,
            ],
        ];

        $response = $this->postJson($payload);
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $text = trim((string) $text);

        if ($text === '') {
            throw new RuntimeException('Gemini no devolvio una respuesta utilizable.');
        }

        return $text;
    }

    private function buildPrompt(string $message, string $catalogContext): string
    {
        return <<<PROMPT
Eres el asistente virtual de Biomedics Souls.
Responde en espanol claro, natural, breve y orientado a ayudar a comprar.
Comportate como un asesor de productos, no como un chatbot generico.
Debes basarte principalmente en el catalogo proporcionado abajo.
No inventes productos, precios, presentaciones, beneficios, stock, promociones o disponibilidad que no esten en el contexto.
Si no tienes suficiente informacion, dilo con honestidad.
No des diagnosticos medicos ni sustituyas consejo profesional.
Cuando recomiendes productos:
- menciona nombres exactos del catalogo
- explica en 1 o 2 lineas por que podrian encajar
- si hay varias opciones, comparalas muy brevemente
- evita listar demasiados productos si no hace falta
Si el usuario es ambiguo, haz una sola pregunta de aclaracion corta.
Si pregunta por precios, menciona el precio exacto del contexto si existe.
Si pregunta por disponibilidad, usa solo el estatus del contexto.
Si no encuentras productos relevantes, dilo y sugiere reformular la necesidad.
Evita frases roboticas, disclaimers largos y relleno.
No uses Markdown complejo; texto plano o listas cortas esta bien.

CATALOGO DISPONIBLE:
{$catalogContext}

PREGUNTA DEL USUARIO:
{$message}
PROMPT;
    }

    private function buildCatalogContext(array $products): string
    {
        if (empty($products)) {
            return 'No se encontraron productos relevantes en la base de datos.';
        }

        $lines = [];
        foreach ($products as $product) {
            $price = isset($product['precio_descuento']) && $product['precio_descuento'] !== null
                ? (float) $product['precio_descuento']
                : (float) ($product['precio'] ?? 0);

            $lines[] = sprintf(
                '- %s | categoria: %s | precio: $%s | estatus: %s | presentacion: %s | descripcion corta: %s | beneficios: %s | modo de empleo: %s',
                trim((string) ($product['nombre'] ?? 'Producto sin nombre')),
                trim((string) ($product['categoria_nombre'] ?? 'General')),
                number_format($price, 2, '.', ''),
                trim((string) ($product['estatus'] ?? 'activo')),
                trim((string) ($product['contenido_neto'] ?? $product['cantidad_envase'] ?? 'No especificado')),
                trim((string) ($product['descripcion_corta'] ?? 'Sin descripcion corta')),
                trim((string) ($product['beneficios'] ?? 'No especificados')),
                trim((string) ($product['modo_empleo'] ?? 'No especificado'))
            );
        }

        return implode("\n", $lines);
    }

    private function buildFollowUpSuggestions(string $message, array $products): array
    {
        $messageLower = function_exists('mb_strtolower')
            ? mb_strtolower($message, 'UTF-8')
            : strtolower($message);

        $suggestions = [];

        if (str_contains($messageLower, 'precio') || str_contains($messageLower, 'cuesta')) {
            $suggestions[] = 'Cual me conviene mas por beneficios';
            $suggestions[] = 'Muestrame otra opcion similar';
        } elseif (str_contains($messageLower, 'energ') || str_contains($messageLower, 'cansancio')) {
            $suggestions[] = 'Quiero comparar opciones para energia';
            $suggestions[] = 'Cual es mejor para uso diario';
        } elseif (str_contains($messageLower, 'enfoque') || str_contains($messageLower, 'concentr')) {
            $suggestions[] = 'Comparame opciones para enfoque mental';
            $suggestions[] = 'Cual recomiendas para estudiar o trabajar';
        } elseif (str_contains($messageLower, 'estres') || str_contains($messageLower, 'estrés') || str_contains($messageLower, 'ansiedad')) {
            $suggestions[] = 'Busco algo para calma y descanso';
            $suggestions[] = 'Que opcion es mas suave para empezar';
        }

        foreach (array_slice($products, 0, 2) as $product) {
            $name = trim((string) ($product['nombre'] ?? ''));
            if ($name !== '') {
                $suggestions[] = 'Cuentame mas sobre ' . $name;
            }
        }

        $suggestions[] = 'Muestrame productos destacados';
        $suggestions[] = 'Que opcion recomiendas segun mi objetivo';

        $suggestions = array_values(array_unique(array_filter(array_map('trim', $suggestions))));
        return array_slice($suggestions, 0, 4);
    }

    private function postJson(array $payload): array
    {
        $headers = [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $this->apiKey,
        ];

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            throw new RuntimeException('No se pudo serializar la solicitud a Gemini.');
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($this->endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_TIMEOUT => 25,
            ]);

            $raw = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($raw === false) {
                throw new RuntimeException('No se pudo conectar con Gemini: ' . $error);
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers),
                    'content' => $body,
                    'timeout' => 25,
                    'ignore_errors' => true,
                ],
            ]);

            $raw = @file_get_contents($this->endpoint, false, $context);
            $statusLine = $http_response_header[0] ?? '';
            preg_match('/\s(\d{3})\s/', $statusLine, $matches);
            $httpCode = isset($matches[1]) ? (int) $matches[1] : 0;

            if ($raw === false) {
                throw new RuntimeException('No se pudo conectar con Gemini.');
            }
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new RuntimeException('Gemini devolvio una respuesta invalida.');
        }

        if ($httpCode >= 400) {
            $message = $data['error']['message'] ?? 'Error desconocido al consultar Gemini.';
            throw new RuntimeException($message);
        }

        return $data;
    }
}
