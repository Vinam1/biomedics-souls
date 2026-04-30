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
        $categories = Categoria::all();
        $filters = $this->catalogFilters();
        $products = Producto::search($filters);

        if ($this->wantsJson()) {
            $html = $this->renderCatalogGridHtml($products);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'html' => $html,
                'count' => count($products),
            ]);
            exit;
        }

        $this->view('pages/catalogo', [
            'title' => 'Catálogo de Suplementos | Sensea',
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => null,
            'filters' => $filters,
        ]);
    }

    public function category(string $slug): void
    {
        $category = Categoria::findBySlug($slug);

        if (!$category) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Categoría no encontrada',
            ]);
            return;
        }

        $products = Producto::findByCategorySlug($slug);
        $categories = Categoria::all();

        $this->view('pages/catalogo', [
            'title' => 'Categoría: ' . htmlspecialchars($category['nombre']),
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
            'filters' => [
                'query' => '',
                'categoria_id' => (int) ($category['id'] ?? 0),
                'sort' => 'updated_at_desc',
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

    public function contact(): void
    {
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();
            $success = 'Gracias por tu mensaje. Te contactaremos pronto.';
        }

        $this->view('pages/contacto', [
            'title' => 'Contáctanos | Biomedics Souls',
            'success' => $success,
        ]);
    }

    public function quiz(): void
    {
        $this->view('pages/quiz', [
            'title' => 'Descubre tu Fórmula | Biomedics Souls',
        ]);
    }

    public function account(): void
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            header('Location: ' . site_url('auth/login'));
            exit;
        }

        $allowedTabs = ['dashboard', 'pedidos', 'pagos', 'direcciones', 'config'];
        $tab = $_GET['tab'] ?? 'dashboard';
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'dashboard';
        }

        $orders = Pedido::findByClienteId((int) $user['id']);
        $reviews = method_exists('Resena', 'findByUser') ? Resena::findByUser((int) $user['id']) : [];
        $addresses = Direccion::allByClienteId((int) $user['id']);
        $paymentMethods = MetodoPago::allByClienteId((int) $user['id']);

        $editingAddress = null;
        if (isset($_GET['edit_address'])) {
            $editingAddress = Direccion::findByIdForCliente((int) $_GET['edit_address'], (int) $user['id']);
        }

        $editingPaymentMethod = null;
        if (isset($_GET['edit_payment'])) {
            $editingPaymentMethod = MetodoPago::findByIdForCliente((int) $_GET['edit_payment'], (int) $user['id']);
        }

        $flash = $_SESSION['account_flash'] ?? null;
        unset($_SESSION['account_flash']);

        $this->view('user/cuenta', [
            'title' => 'Mi Cuenta | Biomedics Souls',
            'user' => $user,
            'tab' => $tab,
            'orders' => $orders,
            'addresses' => $addresses,
            'paymentMethods' => $paymentMethods,
            'reviews' => $reviews,
            'flash' => $flash,
            'editingAddress' => $editingAddress,
            'editingPaymentMethod' => $editingPaymentMethod,
        ]);
    }

    private function catalogFilters(): array
    {
        $sortMap = [
            'recent' => 'updated_at_desc',
            'price_asc' => 'precio_asc',
            'price_desc' => 'precio_desc',
            'name_asc' => 'nombre_asc',
            'name_desc' => 'nombre_desc',
        ];

        $query = trim((string) ($_GET['q'] ?? ''));
        $categoryId = (int) ($_GET['categoria'] ?? 0);
        $sortKey = (string) ($_GET['sort'] ?? 'recent');

        return [
            'query' => $query,
            'categoria_id' => $categoryId > 0 ? $categoryId : null,
            'sort' => $sortMap[$sortKey] ?? 'updated_at_desc',
        ];
    }

    private function wantsJson(): bool
    {
        if (($_GET['ajax'] ?? '') === '1') {
            return true;
        }

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return is_string($accept) && str_contains($accept, 'application/json');
    }

    private function renderCatalogGridHtml(array $products): string
    {
        ob_start();
        require APPROOT . '/views/pages/partials/catalog-product-grid.php';
        return (string) ob_get_clean();
    }
}
