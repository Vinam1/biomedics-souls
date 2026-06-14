<?php

class PaymentGatewayService
{
    public static function process(array $paymentMethod, float $amount, string $orderNumber, array $extra = []): array
    {
        $type = $paymentMethod['tipo'] ?? 'otro';

        if ($type === 'openpay' || ($type === 'tarjeta' && !empty($extra['card_token']))) {
            return OpenPayService::processPayment($paymentMethod, $amount, $orderNumber, $extra);
        }

        $gateway = self::resolveGateway($type);
        $reference = strtoupper($gateway) . '-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $result = [
            'gateway' => $gateway,
            'transaction_id' => $reference,
            'status' => 'approved',
            'detail' => 'Pago aprobado correctamente.',
            'currency' => 'MXN',
            'amount' => round($amount, 2),
            'order_number' => $orderNumber,
            'preference_id' => null,
        ];

        if ($type === 'openpay') {
            $result['preference_id'] = 'op_' . bin2hex(random_bytes(5));
            $result['detail'] = 'Transaccion procesada por OpenPay.';
        }

        if (in_array($type, ['spei', 'oxxo', 'transferencia', 'otro'], true)) {
            $result['status'] = 'pending';
            $result['detail'] = 'Se genero la referencia de pago y el pedido quedo en espera de confirmacion.';
        }

        return $result;
    }

    private static function resolveGateway(string $type): string
    {
        return match ($type) {
            'openpay' => 'openpay',
            'tarjeta' => 'card_gateway',
            'spei' => 'spei_gateway',
            'oxxo' => 'oxxo_gateway',
            'transferencia' => 'bank_transfer',
            default => 'custom_gateway',
        };
    }
}
