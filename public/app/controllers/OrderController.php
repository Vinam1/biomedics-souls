<?php

class OrderController extends Controller
{
    public function confirm(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();
        $user = $this->requireUser();
        $items = Cart::getItems();

        if (empty($items)) {
            header('Location: ' . site_url('carrito'));
            exit;
        }

        if (!empty(Cart::getOutOfStockItems())) {
            $_SESSION['error'] = 'Hay productos agotados en tu carrito. Revísalo antes de confirmar tu pedido.';
            header('Location: ' . site_url('carrito'));
            exit;
        }

        $checkout = $_SESSION['checkout'] ?? [];
        $addressId = (int) ($checkout['address_id'] ?? 0);
        $paymentId = (int) ($checkout['payment_id'] ?? 0);

        $address = Direccion::findByIdForCliente($addressId, (int) $user['id']);
        $paymentMethod = MetodoPago::findByIdForCliente($paymentId, (int) $user['id']);
        $checkout = $_SESSION['checkout'] ?? [];

        if (!$address) {
            header('Location: ' . site_url('checkout?step=1'));
            exit;
        }

        if (!$paymentMethod && !empty($checkout['payment_type']) && $checkout['payment_type'] === 'tarjeta') {
            $paymentMethod = [
                'id' => 0,
                'tipo' => 'tarjeta',
                'brand' => null,
                'ultimo_cuatro' => null,
                'tipo_tarjeta' => null,
                'nickname' => 'Tarjeta de Crédito/Débito',
            ];
        }

        if (!$paymentMethod) {
            header('Location: ' . site_url('checkout?step=2'));
            exit;
        }

        $extra = [
            'user' => $user,
            'card_token' => trim($_POST['card_token'] ?? ''),
            'card_brand' => trim($_POST['card_brand'] ?? ''),
            'payment_method_id' => trim($_POST['payment_method_id'] ?? ''),
            'cardholder_name' => trim($_POST['cardholder_name'] ?? ''),
            'cardholder_email' => trim($_POST['cardholder_email'] ?? $user['email'] ?? ''),
            'doc_type' => trim($_POST['doc_type'] ?? ''),
            'doc_number' => trim($_POST['doc_number'] ?? ''),
        ];

        $draftOrderNumber = Pedido::generateOrderNumber();
        $paymentResult = PaymentGatewayService::process($paymentMethod, Cart::getTotal(), $draftOrderNumber, $extra);
        $paymentResult['order_number'] = $draftOrderNumber;

        try {
            $orderId = Pedido::createDirect($user, $address, $paymentMethod, $items, $paymentResult);
        } catch (Throwable $exception) {
            error_log('Order confirmation failed: ' . $exception->getMessage());
            header('Location: ' . site_url('pedido/fallo'));
            exit;
        }

        Cart::clear();
        unset($_SESSION['checkout']);
        $_SESSION['last_order_id'] = $orderId;

        header('Location: ' . site_url('pedido/exito?order_id=' . $orderId));
        exit;
    }

    public function success(): void
    {
        $user = $this->requireUser();
        $orderId = (int) ($_GET['order_id'] ?? ($_SESSION['last_order_id'] ?? 0));
        $order = $orderId > 0 ? Pedido::findByIdForCliente($orderId, (int) $user['id']) : null;

        if (!$order) {
            header('Location: ' . site_url('cuenta/pedidos'));
            exit;
        }

        $items = Pedido::items((int) $order['id']);
        $transaction = PagoTransaccion::latestByPedidoId((int) $order['id']);

        $this->view('pedido/exito', [
            'title' => 'Pedido Exitoso',
            'order' => $order,
            'items' => $items,
            'transaction' => $transaction,
        ]);
    }

    public function detail(string $id): void
    {
        $user = $this->requireUser();
        $order = Pedido::findByIdForCliente((int) $id, (int) $user['id']);

        if (!$order) {
            $this->renderError(404, 'Pedido no encontrado', 'No encontramos el pedido solicitado en tu cuenta.');
            return;
        }

        $this->view('pedido/detalle', [
            'title' => 'Detalle del pedido',
            'order' => $order,
            'items' => Pedido::items((int) $order['id']),
            'transaction' => PagoTransaccion::latestByPedidoId((int) $order['id']),
        ]);
    }

    public function ticket(string $id): void
    {
        $user = $this->requireUser();
        $order = Pedido::findByIdForCliente((int) $id, (int) $user['id']);

        if (!$order) {
            http_response_code(404);
            echo 'Pedido no encontrado.';
            return;
        }

        $pdf = TicketPdfService::render(
            $order,
            Pedido::items((int) $order['id']),
            PagoTransaccion::latestByPedidoId((int) $order['id'])
        );

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="ticket-' . $order['numero_pedido'] . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    public function failure(): void
    {
        $this->view('pedido/fallo', [
            'title' => 'Pago Fallido',
        ]);
    }
}
