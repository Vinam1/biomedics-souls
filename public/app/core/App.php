<?php

class App
{
    public function __construct()
    {
        $url = $this->parseUrl();

        if (empty($url[0]) || $url[0] === 'home') {
            $this->callController('PageController', 'home');
            return;
        }

        if ($url[0] === 'catalogo') {
            $this->callController('PageController', 'catalog');
            return;
        }

        if (($url[0] === 'product' || $url[0] === 'producto') && isset($url[1])) {
            if (isset($url[2]) && $url[2] === 'review') {
                $this->callController('ProductController', 'addReview', [$url[1]]);
                return;
            }
            $this->callController('ProductController', 'show', [$url[1]]);
            return;
        }

        if ($url[0] === 'category' && isset($url[1])) {
            $this->callController('PageController', 'category', [$url[1]]);
            return;
        }

        if ($url[0] === 'carrito') {
            if (isset($url[1]) && $url[1] === 'agregar' && isset($url[2])) {
                $this->callController('CartController', 'add', [$url[2]]);
                return;
            }
            if (isset($url[1]) && $url[1] === 'remover' && isset($url[2])) {
                $this->callController('CartController', 'remove', [$url[2]]);
                return;
            }
            if (isset($url[1]) && $url[1] === 'actualizar' && isset($url[2])) {
                $this->callController('CartController', 'update', [$url[2]]);
                return;
            }
            $this->callController('CartController', 'index');
            return;
        }

        if ($url[0] === 'checkout') {
            $this->callController('CartController', 'checkout');
            return;
        }

        if ($url[0] === 'ciencia' || $url[0] === 'investigacion') {
            $this->callController('PageController', 'science');
            return;
        }

        if ($url[0] === 'faq') {
            $this->callController('PageController', 'faq');
            return;
        }

        if ($url[0] === 'contacto') {
            $this->callController('PageController', 'contact');
            return;
        }

        if ($url[0] === 'quiz') {
            $this->callController('PageController', 'quiz');
            return;
        }

        if ($url[0] === 'cuenta') {
            if (!isset($url[1])) {
                $this->callController('PageController', 'account');
                return;
            }

            if ($url[1] === 'perfil') {
                $this->callController('AccountController', 'updateProfile');
                return;
            }

            if ($url[1] === 'direccion-guardar') {
                $this->callController('AccountController', 'saveAddress');
                return;
            }

            if ($url[1] === 'direccion-eliminar' && isset($url[2])) {
                $this->callController('AccountController', 'deleteAddress', [$url[2]]);
                return;
            }

            if ($url[1] === 'pago-guardar') {
                $this->callController('AccountController', 'savePaymentMethod');
                return;
            }

            if ($url[1] === 'pago-eliminar' && isset($url[2])) {
                $this->callController('AccountController', 'deletePaymentMethod', [$url[2]]);
                return;
            }

            $this->callController('PageController', 'account');
            return;
        }

        if ($url[0] === 'auth' && isset($url[1])) {
            if ($url[1] === 'login') {
                $this->callController('AuthController', 'login');
                return;
            }
            if ($url[1] === 'register') {
                $this->callController('AuthController', 'register');
                return;
            }
            if ($url[1] === 'logout') {
                $this->callController('AuthController', 'logout');
                return;
            }
        }

        if ($url[0] === 'admin') {
            if (!isset($url[1]) || $url[1] === 'dashboard') {
                $this->callController('AdminController', 'dashboard');
                return;
            }

            if ($url[1] === 'productos') {
                $this->callController('AdminProductController', 'index');
                return;
            }

            if ($url[1] === 'producto-form') {
                $this->callController('AdminProductController', 'form', [$url[2] ?? null]);
                return;
            }

            if ($url[1] === 'producto-eliminar' && isset($url[2])) {
                $this->callController('AdminProductController', 'delete', [$url[2]]);
                return;
            }

            if ($url[1] === 'atributos') {
                if (!isset($url[2]) || $url[2] === 'etiquetas') {
                    $this->callController('AdminAttributesController', 'list', ['etiquetas']);
                    return;
                }
            }

            if ($url[1] === 'atributo-form' && isset($url[2]) && $url[2] === 'etiquetas') {
                $this->callController('AdminAttributesController', 'form', ['etiquetas', $url[3] ?? null]);
                return;
            }

            if ($url[1] === 'atributo-agregar' && isset($url[2]) && $url[2] === 'etiquetas') {
                $this->callController('AdminAttributesController', 'quickAdd', ['etiquetas']);
                return;
            }

            if ($url[1] === 'atributo-actualizar' && isset($url[2]) && $url[2] === 'etiquetas') {
                $this->callController('AdminAttributesController', 'getUpdated', ['etiquetas']);
                return;
            }

            if ($url[1] === 'atributo-eliminar' && isset($url[2]) && $url[2] === 'etiquetas' && isset($url[3])) {
                $this->callController('AdminAttributesController', 'delete', ['etiquetas', $url[3]]);
                return;
            }

            if ($url[1] === 'categorias') {
                $this->callController('AdminController', 'categorias');
                return;
            }

            if ($url[1] === 'categoria-form') {
                $this->callController('AdminController', 'categoriaForm', [$url[2] ?? null]);
                return;
            }

            if ($url[1] === 'categoria-eliminar' && isset($url[2])) {
                $this->callController('AdminController', 'categoriaEliminar', [$url[2]]);
                return;
            }

            if ($url[1] === 'pedidos') {
                $this->callController('AdminController', 'pedidos');
                return;
            }

            if ($url[1] === 'pedido-detalle' && isset($url[2])) {
                $this->callController('AdminController', 'pedidoDetalle', [$url[2]]);
                return;
            }

            $this->callController('AdminController', 'dashboard');
            return;
        }

        if ($url[0] === 'pedido' && isset($url[1])) {
            if ($url[1] === 'confirmar') {
                $this->callController('OrderController', 'confirm');
                return;
            }
            if ($url[1] === 'exito') {
                $this->callController('OrderController', 'success');
                return;
            }
            if ($url[1] === 'fallo') {
                $this->callController('OrderController', 'failure');
                return;
            }
            if ($url[1] === 'detalle' && isset($url[2])) {
                $this->callController('OrderController', 'detail', [$url[2]]);
                return;
            }
            if ($url[1] === 'ticket' && isset($url[2])) {
                $this->callController('OrderController', 'ticket', [$url[2]]);
                return;
            }
        }

        $this->render404();
    }

    private function parseUrl(): array
    {
        $url = $_GET['url'] ?? '';
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url === '' ? [] : explode('/', $url);
    }

    private function callController(string $controller, string $method, array $params = []): void
    {
        if (!class_exists($controller)) {
            $this->render404();
            return;
        }

        $controllerObject = new $controller();

        if (!method_exists($controllerObject, $method)) {
            $this->render404();
            return;
        }

        call_user_func_array([$controllerObject, $method], $params);
    }

    private function render404(): void
    {
        http_response_code(404);
        $title = 'PÃ¡gina no encontrada';
        $viewFile = APPROOT . '/views/errors/404.php';
        require APPROOT . '/views/layouts/main.php';
    }
}
