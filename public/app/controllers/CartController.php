<?php

class CartController extends Controller
{
    public function index(): void
    {
        $items = Cart::getItems();
        $total = Cart::getTotal();
        $flashSuccess = $_SESSION['success'] ?? null;
        $flashError = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('pages/carrito', [
            'title' => 'Carrito de Compras | Biomedcs Souls',
            'cartItems' => $items,
            'total' => $total,
            'flashSuccess' => $flashSuccess,
            'flashError' => $flashError,
            'hasOutOfStockItems' => !empty(Cart::getOutOfStockItems()),
        ]);
    }

    public function add(string $productId): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        $quantity = (int) ($_POST['quantity'] ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $product = Producto::findById((int) $productId);
        if (!$product) {
            header('Location: ' . site_url('carrito'));
            exit;
        }

        if (Producto::isOutOfStockStatus($product['estatus'] ?? null)) {
            $_SESSION['error'] = 'Este producto está agotado y no se puede agregar al carrito.';
            header('Location: ' . $this->getRedirectBackUrl(site_url('producto/' . $product['slug'])));
            exit;
        }

        Cart::add((int) $productId, $quantity);
        $_SESSION['success'] = 'Producto agregado al carrito.';
        header('Location: ' . site_url('carrito'));
        exit;
    }

    public function remove(string $productId): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        Cart::remove((int) $productId);
        header('Location: ' . site_url('carrito'));
        exit;
    }

    public function update(string $productId): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        $quantity = (int) ($_POST['quantity'] ?? 0);
        $currentQuantity = (int) ($_SESSION['cart'][(int) $productId] ?? 0);
        $product = Producto::findById((int) $productId);

        if ($product && Producto::isOutOfStockStatus($product['estatus'] ?? null) && $quantity > $currentQuantity) {
            $_SESSION['error'] = 'Este producto está agotado y no puedes aumentar su cantidad.';
            header('Location: ' . site_url('carrito'));
            exit;
        }

        if ($quantity > 0) {
            Cart::setQuantity((int) $productId, $quantity);
        } else {
            Cart::remove((int) $productId);
        }

        header('Location: ' . site_url('carrito'));
        exit;
    }

    public function checkout(): void
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            $_SESSION['account_flash'] = [
                'type' => 'error',
                'message' => 'Inicia sesión para usar tus datos guardados en el checkout.',
            ];
            header('Location: ' . site_url('auth/login'));
            exit;
        }

        $items = Cart::getItems();
        if (empty($items)) {
            header('Location: ' . site_url('carrito'));
            exit;
        }

        if (!empty(Cart::getOutOfStockItems())) {
            $_SESSION['error'] = 'Hay productos agotados en tu carrito. Elimínalos antes de continuar al checkout.';
            header('Location: ' . site_url('carrito'));
            exit;
        }

        $total = Cart::getTotal();
        $step = max(1, min(3, (int) ($_GET['step'] ?? 1)));
        $addresses = Direccion::allByClienteId((int) $user['id']);
        $paymentMethods = MetodoPago::allByClienteId((int) $user['id']);
        $_SESSION['checkout'] = $_SESSION['checkout'] ?? [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();

            if ($step === 1) {
                $addressId = (int) ($_POST['address_id'] ?? 0);
                $selectedAddress = Direccion::findByIdForCliente($addressId, (int) $user['id']);
                if (!$selectedAddress) {
                    $this->renderError(422, 'Dirección requerida', 'Selecciona una dirección guardada antes de continuar al pago.');
                    return;
                }

                $_SESSION['checkout']['address_id'] = $selectedAddress['id'];
                header('Location: ' . site_url('checkout?step=2'));
                exit;
            }

            if ($step === 2) {
                $paymentId = (int) ($_POST['payment_id'] ?? 0);
                $selectedPaymentMethod = MetodoPago::findByIdForCliente($paymentId, (int) $user['id']);
                if (!$selectedPaymentMethod) {
                    $this->renderError(422, 'Método de pago requerido', 'Selecciona un método de pago guardado antes de confirmar tu pedido.');
                    return;
                }

                $_SESSION['checkout']['payment_id'] = $selectedPaymentMethod['id'];
                header('Location: ' . site_url('checkout?step=3'));
                exit;
            }
        }

        $selectedAddress = null;
        if (!empty($_SESSION['checkout']['address_id'])) {
            $selectedAddress = Direccion::findByIdForCliente((int) $_SESSION['checkout']['address_id'], (int) $user['id']);
        }
        if (!$selectedAddress && !empty($addresses)) {
            $selectedAddress = Direccion::defaultForCliente((int) $user['id']);
            if ($selectedAddress) {
                $_SESSION['checkout']['address_id'] = $selectedAddress['id'];
            }
        }

        $selectedPaymentMethod = null;
        if (!empty($_SESSION['checkout']['payment_id'])) {
            $selectedPaymentMethod = MetodoPago::findByIdForCliente((int) $_SESSION['checkout']['payment_id'], (int) $user['id']);
        }
        if (!$selectedPaymentMethod && !empty($paymentMethods)) {
            $selectedPaymentMethod = MetodoPago::defaultForCliente((int) $user['id']);
            if ($selectedPaymentMethod) {
                $_SESSION['checkout']['payment_id'] = $selectedPaymentMethod['id'];
            }
        }

        if ($step > 1 && !$selectedAddress) {
            header('Location: ' . site_url('checkout?step=1'));
            exit;
        }

        if ($step > 2 && !$selectedPaymentMethod) {
            header('Location: ' . site_url('checkout?step=2'));
            exit;
        }

        $this->view('pages/checkout', [
            'title' => 'Checkout | Biomedics Souls',
            'cartItems' => $items,
            'total' => $total,
            'user' => $user,
            'step' => $step,
            'addresses' => $addresses,
            'paymentMethods' => $paymentMethods,
            'selectedAddress' => $selectedAddress,
            'selectedPaymentMethod' => $selectedPaymentMethod,
        ]);
    }

    private function getRedirectBackUrl(string $fallback): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (!is_string($referer) || $referer === '') {
            return $fallback;
        }

        if (!str_starts_with($referer, site_url())) {
            return $fallback;
        }

        return $referer;
    }
}
