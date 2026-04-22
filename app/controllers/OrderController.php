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

        $checkout = $_SESSION['checkout'] ?? [];
        $addressId = (int) ($checkout['address_id'] ?? 0);
        $paymentId = (int) ($checkout['payment_id'] ?? 0);

        $address = Direccion::findByIdForCliente($addressId, (int) $user['id']);
        $paymentMethod = MetodoPago::findByIdForCliente($paymentId, (int) $user['id']);

        if (!$address) {
            header('Location: ' . site_url('checkout?step=1'));
            exit;
        }

        if (!$paymentMethod) {
            header('Location: ' . site_url('checkout?step=2'));
            exit;
        }

        $draftOrderNumber = Pedido::generateOrderNumber();
        $paymentResult = PaymentGatewayService::process($paymentMethod, Cart::getTotal(), $draftOrderNumber);
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
            header('Location: ' . site_url('cuenta?tab=pedidos'));
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

    private function requireUser(): array
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            header('Location: ' . site_url('auth/login'));
            exit;
        }

        return $user;
    }
}
