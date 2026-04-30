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

    public function account(): void
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            header('Location: ' . site_url('auth/login'));
            exit;
        }

        $tab = $_GET['tab'] ?? 'dashboard';

        $this->view('user/cuenta', [
            'title'  => 'Mi Cuenta | Biomedics Souls',
            'user'   => $user,
            'tab'    => $tab,
            'orders' => Pedido::findByClienteId((int) $user['id']),
        ]);
    }

    // ====================== HELPERS ======================

    private function catalogFilters(): array
    {
        $query      = trim((string) ($_GET['q'] ?? ''));
        $categoryId = (int) ($_GET['categoria'] ?? 0);
        $sortInput  = (string) ($_GET['sort'] ?? 'recent');

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