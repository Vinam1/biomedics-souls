<?php

class MercadoPagoService
{
    public static function processPayment(array $paymentMethod, float $amount, string $orderNumber, array $extra = []): array
    {
        if (!self::isConfigured()) {
            return self::buildResponse('mercado_pago', '', 'rejected', 'Mercado Pago no está configurado en el servidor.', 'MP_ACCESS_TOKEN no encontrado.');
        }

        $token = trim($extra['card_token'] ?? '');
        $paymentMethodId = trim($extra['payment_method_id'] ?? '');
        $cardBrand = trim($extra['card_brand'] ?? '');
        $user = $extra['user'] ?? [];
        $cardholderEmail = trim($extra['cardholder_email'] ?? $user['email'] ?? '');
        $cardholderName = trim($extra['cardholder_name'] ?? trim(($user['nombre'] ?? '') . ' ' . ($user['apellidos'] ?? '')));
        $docType = trim($extra['doc_type'] ?? '');
        $docNumber = trim($extra['doc_number'] ?? '');

        if ($token === '') {
            return self::buildResponse('mercado_pago', '', 'rejected', 'No se recibió el token de tarjeta. Intenta de nuevo.', 'missing_card_token');
        }

        if ($paymentMethodId === '') {
            $paymentMethodId = strtolower($cardBrand ?: 'visa');
        }

        $payer = [
            'email' => $cardholderEmail,
        ];

        if ($cardholderName !== '') {
            $parts = preg_split('/\s+/', $cardholderName, 2, PREG_SPLIT_NO_EMPTY);
            $payer['first_name'] = $parts[0] ?? '';
            if (!empty($parts[1])) {
                $payer['last_name'] = $parts[1];
            }
        }

        if ($docType !== '' && $docNumber !== '') {
            $payer['identification'] = [
                'type' => strtoupper($docType),
                'number' => $docNumber,
            ];
        }

        $payload = [
            'transaction_amount' => round($amount, 2),
            'token' => $token,
            'description' => 'Pedido ' . $orderNumber,
            'installments' => 1,
            'payment_method_id' => $paymentMethodId,
            'payer' => $payer,
            'statement_descriptor' => 'BIOMEDICSSOULS',
        ];

        try {
            $response = self::post('/v1/payments', $payload);
        } catch (RuntimeException $exception) {
            return self::buildResponse('mercado_pago', '', 'rejected', 'Error de comunicación con Mercado Pago: ' . $exception->getMessage(), 'communication_error');
        }

        $transactionId = trim((string) ($response['id'] ?? ''));
        $status = trim((string) ($response['status'] ?? 'rejected'));
        $statusDetail = trim((string) ($response['status_detail'] ?? $response['response_message'] ?? 'Sin detalle de estado'));
        $currency = trim((string) ($response['currency_id'] ?? 'MXN')) ?: 'MXN';
        $paidAmount = isset($response['transaction_details']['total_paid_amount']) ? (float) $response['transaction_details']['total_paid_amount'] : round($amount, 2);

        $mappedStatus = self::mapStatus($status);

        return [
            'gateway' => 'mercado_pago',
            'transaction_id' => $transactionId,
            'status' => $mappedStatus,
            'detail' => $statusDetail,
            'currency' => $currency,
            'amount' => $paidAmount,
            'order_number' => $orderNumber,
            'preference_id' => $response['preference_id'] ?? null,
        ];
    }

    private static function isConfigured(): bool
    {
        return defined('MP_ACCESS_TOKEN') && MP_ACCESS_TOKEN !== '' && defined('MP_BASE_URL') && MP_BASE_URL !== '';
    }

    private static function mapStatus(string $status): string
    {
        if ($status === 'approved') {
            return 'approved';
        }

        if (in_array($status, ['in_process', 'pending', 'authorized'], true)) {
            return 'pending';
        }

        return 'rejected';
    }

    private static function post(string $path, array $payload): array
    {
        $url = rtrim(MP_BASE_URL, '/') . $path;
        $json = json_encode($payload);

        if ($json === false) {
            throw new RuntimeException('No se pudo serializar el payload de Mercado Pago.');
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            ]);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $responseBody = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($responseBody === false || $httpStatus >= 400) {
                $errorMessage = $curlError ?: 'HTTP ' . $httpStatus . ' desde Mercado Pago.';
                throw new RuntimeException($errorMessage);
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json\r\n' .
                        'Authorization: Bearer ' . MP_ACCESS_TOKEN . '\r\n',
                    'content' => $json,
                    'timeout' => 30,
                ],
            ]);
            $responseBody = file_get_contents($url, false, $context);
            if ($responseBody === false) {
                throw new RuntimeException('No se pudo conectar a Mercado Pago.');
            }
        }

        $response = json_decode($responseBody, true);
        if (!is_array($response)) {
            throw new RuntimeException('Respuesta de Mercado Pago no válida.');
        }

        if (isset($response['errors']) && is_array($response['errors'])) {
            $messages = array_map(function ($error) {
                return $error['message'] ?? ($error['detail'] ?? json_encode($error));
            }, $response['errors']);
            throw new RuntimeException(implode(' | ', $messages));
        }

        return $response;
    }

    private static function buildResponse(string $gateway, string $transactionId, string $status, string $detail, string $debugCode = null): array
    {
        $response = [
            'gateway' => $gateway,
            'transaction_id' => $transactionId,
            'status' => $status,
            'detail' => $detail,
            'currency' => 'MXN',
            'amount' => 0.0,
            'order_number' => '',
            'preference_id' => null,
        ];

        if ($debugCode !== null) {
            $response['detail'] .= ' (' . $debugCode . ')';
        }

        return $response;
    }
}
