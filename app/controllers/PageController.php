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

        $this->view('pages/catalogo', [
            'title' => 'Catálogo de Suplementos | Sensea',
            'products' => Producto::allActive(),
            'categories' => $categories,
            'currentCategory' => null,
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
}
