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
        $this->verifyCsrfOrAbort($this->wantsJson());

        $quantity = (int) ($_POST['quantity'] ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $product = Producto::findById((int) $productId);
        if (!$product) {
            if ($this->wantsJson()) {
                http_response_code(404);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'El producto no existe o ya no está disponible.',
                ]);
                exit;
            }

            header('Location: ' . site_url('carrito'));
            exit;
        }

        if (Producto::isOutOfStockStatus($product['estatus'] ?? null)) {
            $message = 'Este producto esta agotado y no se puede agregar al carrito.';

            if ($this->wantsJson()) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'success' => false,
                    'message' => $message,
                ]);
                exit;
            }

            $_SESSION['cart_feedback'] = [
                'type' => 'error',
                'message' => $message,
            ];
            header('Location: ' . $this->getRedirectTarget(site_url('producto/' . $product['slug'])));
            exit;
        }

        Cart::add((int) $productId, $quantity);
        $productName = trim((string) ($product['nombre'] ?? $product['name'] ?? 'Producto'));
        $message = $quantity > 1
            ? $productName . ' agregado al carrito (' . $quantity . ' unidades).'
            : $productName . ' agregado al carrito.';

        if ($this->wantsJson()) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => $message,
                'cartCount' => Cart::getCount(),
            ]);
            exit;
        }

        $_SESSION['cart_feedback'] = [
            'type' => 'success',
            'message' => $message,
        ];
        header('Location: ' . $this->getRedirectTarget(site_url('carrito')));
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
            $_SESSION['error'] = 'Este producto estÃ¡ agotado y no puedes aumentar su cantidad.';
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
                'message' => 'Inicia sesiÃ³n para usar tus datos guardados en el checkout.',
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
            $_SESSION['error'] = 'Hay productos agotados en tu carrito. ElimÃ­nalos antes de continuar al checkout.';
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
                    $this->renderError(422, 'DirecciÃ³n requerida', 'Selecciona una direcciÃ³n guardada antes de continuar al pago.');
                    return;
                }

                $_SESSION['checkout']['address_id'] = $selectedAddress['id'];
                header('Location: ' . site_url('checkout?step=2'));
                exit;
            }

            if ($step === 2) {
                $paymentId = trim($_POST['payment_id'] ?? '');

                if ($paymentId === 'new_card') {
                    $_SESSION['checkout']['payment_type'] = 'tarjeta';
                    unset($_SESSION['checkout']['payment_id']);
                    header('Location: ' . site_url('checkout?step=3'));
                    exit;
                }

                $selectedPaymentMethod = MetodoPago::findByIdForCliente((int) $paymentId, (int) $user['id']);
                if (!$selectedPaymentMethod) {
                    $this->renderError(422, 'MÃ©todo de pago requerido', 'Selecciona un mÃ©todo de pago guardado antes de confirmar tu pedido.');
                    return;
                }

                $_SESSION['checkout']['payment_id'] = $selectedPaymentMethod['id'];
                unset($_SESSION['checkout']['payment_type']);
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

        if (!$selectedPaymentMethod && !empty($_SESSION['checkout']['payment_type']) && $_SESSION['checkout']['payment_type'] === 'tarjeta') {
            $selectedPaymentMethod = [
                'id' => 0,
                'tipo' => 'tarjeta',
                'brand' => null,
                'ultimo_cuatro' => null,
                'tipo_tarjeta' => null,
                'nickname' => 'Tarjeta de Credito/Debito',
            ];
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
            'openpayPublicKey' => OPENPAY_API_KEY,
            'openpayMerchantId' => OPENPAY_MERCHANT_ID,
        ]);
    }

    private function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return (is_string($accept) && stripos($accept, 'application/json') !== false)
            || $requestedWith === 'XMLHttpRequest';
    }

    private function getRedirectTarget(string $fallback): string
    {
        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($this->isSafeInternalRedirect($redirectTo)) {
            return $redirectTo;
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (is_string($referer) && $referer !== '' && $this->isSafeInternalRedirect($referer)) {
            return $referer;
        }

        return $fallback;
    }

    private function isSafeInternalRedirect(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return false;
        }

        $host = $parts['host'] ?? '';
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        if ($host !== '' && strcasecmp($host, $currentHost) !== 0) {
            return false;
        }

        $path = $parts['path'] ?? '';
        if ($path === '') {
            $path = '/';
        }

        $appPath = parse_url(site_url(), PHP_URL_PATH) ?? '';
        return str_starts_with($path, $appPath);
    }
}
