<?php

class OpenPayService
{
    public static function processPayment(array $paymentMethod, float $amount, string $orderNumber, array $extra = []): array
    {
        if (!self::isConfigured()) {
            return self::buildResponse('openpay', '', 'rejected', 'OpenPay no está configurado en el servidor.', 'OPENPAY_API_KEY no encontrado.');
        }

        $token = trim($extra['card_token'] ?? '');
        $cardBrand = trim($extra['card_brand'] ?? '');
        $user = $extra['user'] ?? [];
        $cardholderEmail = trim($extra['cardholder_email'] ?? $user['email'] ?? '');
        $cardholderName = trim($extra['cardholder_name'] ?? trim(($user['nombre'] ?? '') . ' ' . ($user['apellidos'] ?? '')));
        $docNumber = trim($extra['doc_number'] ?? '');

        if ($token === '') {
            return self::buildResponse('openpay', '', 'rejected', 'No se recibió el token de tarjeta. Intenta de nuevo.', 'missing_card_token');
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

        $payload = [
            'amount' => round($amount, 2),
            'currency' => 'MXN',
            'source_id' => $token,
            'description' => 'Pedido ' . $orderNumber,
            'order_id' => $orderNumber,
            'customer' => $payer,
        ];

        try {
            $response = self::post('/charges', $payload);
        } catch (RuntimeException $exception) {
            return self::buildResponse('openpay', '', 'rejected', 'Error de comunicación con OpenPay: ' . $exception->getMessage(), 'communication_error');
        }

        $transactionId = trim((string) ($response['id'] ?? ''));
        $status = trim((string) ($response['status'] ?? 'failed'));
        $statusDetail = trim((string) ($response['status_message'] ?? 'Sin detalle de estado'));
        $currency = trim((string) ($response['currency'] ?? 'MXN')) ?: 'MXN';
        $paidAmount = isset($response['amount']) ? (float) $response['amount'] : round($amount, 2);

        $mappedStatus = self::mapStatus($status);

        return [
            'gateway' => 'openpay',
            'transaction_id' => $transactionId,
            'status' => $mappedStatus,
            'detail' => $statusDetail,
            'currency' => $currency,
            'amount' => $paidAmount,
            'order_number' => $orderNumber,
            'preference_id' => $response['id'] ?? null,
        ];
    }

    private static function isConfigured(): bool
    {
        return defined('OPENPAY_API_KEY') && OPENPAY_API_KEY !== '' 
            && defined('OPENPAY_MERCHANT_ID') && OPENPAY_MERCHANT_ID !== ''
            && defined('OPENPAY_BASE_URL') && OPENPAY_BASE_URL !== '';
    }

    private static function mapStatus(string $status): string
    {
        if ($status === 'completed') {
            return 'approved';
        }

        if (in_array($status, ['pending', 'processing'], true)) {
            return 'pending';
        }

        return 'rejected';
    }

    private static function post(string $path, array $payload): array
    {
        $merchantId = OPENPAY_MERCHANT_ID;
        $url = rtrim(OPENPAY_BASE_URL, '/') . '/' . $merchantId . $path;
        $json = json_encode($payload);

        if ($json === false) {
            throw new RuntimeException('No se pudo serializar el payload de OpenPay.');
        }

        $auth = OPENPAY_API_KEY . ':';

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $responseBody = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($responseBody === false || $httpStatus >= 400) {
                $errorMessage = $curlError ?: 'HTTP ' . $httpStatus . ' desde OpenPay.';
                throw new RuntimeException($errorMessage);
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json\r\n' .
                        'Accept: application/json\r\n' .
                        'Authorization: Basic ' . base64_encode($auth) . '\r\n',
                    'content' => $json,
                    'timeout' => 30,
                ],
            ]);
            $responseBody = file_get_contents($url, false, $context);
            if ($responseBody === false) {
                throw new RuntimeException('No se pudo conectar a OpenPay.');
            }
        }

        $response = json_decode($responseBody, true);
        if (!is_array($response)) {
            throw new RuntimeException('Respuesta de OpenPay no válida.');
        }

        if (isset($response['error_code']) && !empty($response['error_code'])) {
            $messages = [$response['description'] ?? ($response['error_message'] ?? 'Error desconocido')];
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
