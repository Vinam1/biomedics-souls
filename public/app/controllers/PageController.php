<?php

class PageController extends Controller
{
    public function home(): void
    {
        $this->view('pages/home', [
            'title'    => 'Biomedcs Souls | Sensea - Suplementos Premium',
            'featured' => Producto::featured(8),
        ]);
    }

    public function catalog(): void
{
    try {
        $categories = Categoria::all();
        $filters    = $this->catalogFilters();
        $products   = Producto::search($filters);

        if ($this->wantsJson()) {
            $html = $this->renderCatalogGridHtml($products);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'html'  => $html,
                'count' => count($products),
            ]);
            exit;
        }

        $this->view('pages/catalogo', [
            'title'           => 'Catálogo de Suplementos | Sensea',
            'products'        => $products,
            'categories'      => $categories,
            'currentCategory' => null,
            'filters'         => $filters,
        ]);

    } catch (Throwable $e) {
        // Mostramos el error real en pantalla (solo para depuración)
        echo "<h2>Error en catálogo</h2>";
        echo "<strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>";
        echo "<strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . " (línea " . $e->getLine() . ")<br><br>";
        echo "<strong>Trace:</strong><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        
        error_log("ERROR EN CATALOGO: " . $e->getMessage() . " | " . $e->getFile() . ":" . $e->getLine());
        exit;
    }
}

    public function category(string $slug): void
    {
        $category = Categoria::findBySlug($slug);

        if (!$category) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Categoría no encontrada']);
            return;
        }

        $products   = Producto::findByCategorySlug($slug);
        $categories = Categoria::all();

        $this->view('pages/catalogo', [
            'title'           => 'Categoría: ' . htmlspecialchars($category['nombre']),
            'products'        => $products,
            'categories'      => $categories,
            'currentCategory' => $category,
            'filters'         => [
                'query'        => '',
                'categoria_id' => (int) ($category['id'] ?? 0),
                'sort'         => 'updated_at_desc',
            ],
        ]);
    }

    public function science(): void
    {
        $this->view('pages/ciencia', [
            'title' => 'Ciencia e Investigación | Biomedcs Souls',
        ]);
    }

    public function faq(): void
    {
        $this->view('pages/faq', [
            'title' => 'Preguntas Frecuentes | Biomedcs Souls',
        ]);
    }

    public function privacy(): void
    {
        $this->view('pages/privacidad', [
            'title' => 'Política de Privacidad | Biomedics Souls',
        ]);
    }

    public function contact(): void
    {
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();
            $success = 'Gracias por tu mensaje. Te contactaremos pronto.';
        }

        $this->view('pages/contacto', [
            'title'   => 'Contáctanos | Biomedics Souls',
            'success' => $success,
        ]);
    }

    public function quiz(): void
    {
        $this->view('pages/quiz', [
            'title' => 'Descubre tu Fórmula | Biomedcs Souls',
        ]);
    }

    public function account(string $tab = 'dashboard'): void
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            header('Location: ' . site_url('auth/login'));
            exit;
        }

        $tab = in_array($tab, ['dashboard', 'pedidos', 'pagos', 'direcciones', 'resenas', 'config'], true)
            ? $tab
            : 'dashboard';

        $orders = Pedido::findByClienteId((int) $user['id']);
        $paymentMethods = MetodoPago::allByClienteId((int) $user['id']);
        $addresses = Direccion::allByClienteId((int) $user['id']);
        $reviews = Resena::findByUser((int) $user['id']);
        $reviewableProducts = Resena::reviewableProductsForUser((int) $user['id']);

        $editingAddress = null;
        if ($tab === 'direcciones' && !empty($_GET['edit_address'])) {
            $editingAddress = Direccion::findByIdForCliente((int) $_GET['edit_address'], (int) $user['id']);
        }

        $editingPayment = null;
        if ($tab === 'pagos' && !empty($_GET['edit_payment'])) {
            $editingPayment = MetodoPago::findByIdForCliente((int) $_GET['edit_payment'], (int) $user['id']);
        }

        $flash = $_SESSION['account_flash'] ?? null;
        unset($_SESSION['account_flash']);

        $this->view('user/cuenta', [
            'title' => 'Mi Cuenta | Biomedics Souls',
            'user' => $user,
            'tab' => $tab,
            'orders' => $orders,
            'paymentMethods' => $paymentMethods,
            'addresses' => $addresses,
            'reviews' => $reviews,
            'reviewableProducts' => $reviewableProducts,
            'editingAddress' => $editingAddress,
            'editingPayment' => $editingPayment,
            'flash' => $flash,
        ]);
    }

    // ====================== HELPERS ======================

    private function catalogFilters(): array
    {
        $query      = trim((string) ($_GET['q'] ?? ''));
        $categoryId = (int) ($_GET['categoria'] ?? 0);
        $sortInput  = (string) ($_GET['sort'] ?? 'recent');
        $recommendedIds = array_values(array_unique(array_filter(array_map('intval', explode(',', (string) ($_GET['recommended'] ?? ''))), static function (int $id): bool {
            return $id > 0;
        })));

        $sortMap = [
            'recent'     => 'updated_at_desc',
            'price_asc'  => 'precio_asc',
            'price_desc' => 'precio_desc',
            'name_asc'   => 'nombre_asc',
            'name_desc'  => 'nombre_desc',
        ];

        return [
            'query'        => $query,
            'categoria_id' => $categoryId > 0 ? $categoryId : null,
            'product_ids'  => $recommendedIds,
            'sort'         => $sortMap[$sortInput] ?? 'updated_at_desc',
        ];
    }

    private function wantsJson(): bool
{
    if (($_GET['ajax'] ?? '') === '1') {
        return true;
    }

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    
    // Versión compatible con PHP 7.4 y anteriores
    return is_string($accept) && strpos($accept, 'application/json') !== false;
}

    private function renderCatalogGridHtml(array $products): string
    {
        ob_start();
        require APPROOT . '/views/pages/partials/catalog-product-grid.php';
        return ob_get_clean();
    }
}
