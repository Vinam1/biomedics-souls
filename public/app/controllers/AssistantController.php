<?php

class AssistantController extends Controller
{
    public function chat(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort(true);

        header('Content-Type: application/json; charset=UTF-8');

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Solicitud inválida.',
            ]);
            return;
        }

        $message = trim((string) ($payload['message'] ?? ''));
        if ($message === '') {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Escribe una pregunta para continuar.',
            ]);
            return;
        }

        if (function_exists('mb_strlen') ? mb_strlen($message, 'UTF-8') > 800 : strlen($message) > 800) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Tu mensaje es demasiado largo.',
            ]);
            return;
        }

        $history = $_SESSION['assistant_chat_history'] ?? [];
        $products = Producto::assistantRelevant($message, 8);
        $service = new GeminiChatService();

        try {
            $result = $service->generateChatResult($message, $products, $history);
            $reply = $result['reply'];
            $recommendedProducts = $result['recommendedProducts'] ?? $products;

            $history[] = ['role' => 'user', 'text' => $message];
            $history[] = ['role' => 'assistant', 'text' => $reply];
            $_SESSION['assistant_chat_history'] = array_slice($history, -12);

            echo json_encode([
                'success' => true,
                'reply' => $reply,
                'products' => array_map(static function (array $product): array {
                    return [
                        'name' => $product['nombre'] ?? 'Producto',
                        'slug' => $product['slug'] ?? '',
                        'price' => (float) ($product['precio_descuento'] ?? $product['precio'] ?? 0),
                        'status' => $product['estatus'] ?? '',
                        'image' => !empty($product['imagen_principal'])
                            ? asset_url('img/products/' . $product['imagen_principal'])
                            : null,
                        'url' => site_url('producto/' . ($product['slug'] ?? '')),
                    ];
                }, $recommendedProducts),
                'suggestions' => $result['suggestions'] ?? [],
                'configured' => $service->isConfigured(),
            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            error_log('ASSISTANT CHAT ERROR: ' . $e->getMessage());

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $service->isConfigured()
                    ? 'No pude generar una respuesta en este momento. Intenta de nuevo.'
                    : 'Falta configurar GEMINI_API_KEY en el entorno para activar el chat inteligente.',
                'configured' => $service->isConfigured(),
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function reset(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort(true);
        unset($_SESSION['assistant_chat_history']);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
        ]);
    }
}
