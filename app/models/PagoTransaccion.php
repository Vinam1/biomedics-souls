<?php

class PagoTransaccion
{
    public static function create(int $pedidoId, int $clienteId, int $metodoPagoId, array $paymentResult, float $amount): int
    {
        Database::ensurePaymentTransactionsTable();
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO pagos_transacciones (
                pedido_id, cliente_id, metodo_pago_id, gateway, referencia, estado, detalle, monto, moneda, payload_respuesta
            ) VALUES (
                :pedido_id, :cliente_id, :metodo_pago_id, :gateway, :referencia, :estado, :detalle, :monto, :moneda, :payload_respuesta
            )'
        );

        $stmt->execute([
            'pedido_id' => $pedidoId,
            'cliente_id' => $clienteId,
            'metodo_pago_id' => $metodoPagoId,
            'gateway' => $paymentResult['gateway'] ?? 'manual',
            'referencia' => $paymentResult['transaction_id'] ?? ('txn_' . bin2hex(random_bytes(6))),
            'estado' => $paymentResult['status'] ?? 'approved',
            'detalle' => $paymentResult['detail'] ?? null,
            'monto' => $amount,
            'moneda' => $paymentResult['currency'] ?? 'MXN',
            'payload_respuesta' => json_encode($paymentResult, JSON_UNESCAPED_UNICODE),
        ]);

        return (int) $db->lastInsertId();
    }

    public static function latestByPedidoId(int $pedidoId): ?array
    {
        Database::ensurePaymentTransactionsTable();
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM pagos_transacciones WHERE pedido_id = :pedido_id ORDER BY created_at DESC, id DESC LIMIT 1'
        );
        $stmt->execute(['pedido_id' => $pedidoId]);

        return $stmt->fetch() ?: null;
    }
}
