<?php

class AdminController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function dashboard(): void
    {
        $productCount        = Producto::countAll();
        $totalOrders         = Pedido::countAll();
        $etiquetasCount      = Etiqueta::count();
        $categoriesCount     = Categoria::count();
        $month               = date('Y-m');
        $currentMonthOrders  = Pedido::countByMonth($month);
        $monthlySales        = Pedido::totalSalesByMonth($month);
        $totalSales          = Pedido::totalSales();
        $pendingOrders       = Pedido::getByStatus('pendiente', 6);
        $deliveredOrders     = Pedido::getByStatus('entregado', 6);

        $this->view('admin/dashboard', [
            'title'               => 'Dashboard Admin | Biomedics Souls',
            'productCount'        => $productCount,
            'totalOrders'         => $totalOrders,
            'etiquetasCount'      => $etiquetasCount,
            'categoriesCount'     => $categoriesCount,
            'currentMonthOrders'  => $currentMonthOrders,
            'monthlySales'        => $monthlySales,
            'totalSales'          => $totalSales,
            'pendingOrders'       => $pendingOrders,
            'deliveredOrders'     => $deliveredOrders,
        ]);
    }

    public function pedidos(): void
    {
        $this->view('admin/orders/index', [
            'title'  => 'Pedidos - Admin',
            'orders' => Pedido::all(),
        ]);
    }

    public function pedidoDetalle(string $id): void
    {
        $order = Pedido::findById((int) $id);
        if (!$order) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Pedido no encontrado']);
            return;
        }

        $this->view('admin/orders/show', [
            'title' => 'Detalle de Pedido #' . ($order['numero_pedido'] ?? $id),
            'order' => $order,
            'items' => Pedido::items((int) $id),
        ]);
    }

    public function categorias(): void
    {
        $search = trim($_GET['q'] ?? '');

        $this->view('admin/categories/index', [
            'title'      => 'Gestión de Categorías',
            'categories' => Categoria::search($search),
            'search'     => $search,
        ]);
    }

    public function categoriaForm(?string $id = null): void
    {
        $category = null;
        $error    = null;

        if ($id !== null) {
            $category = Categoria::findById((int) $id);
            if (!$category) {
                http_response_code(404);
                $this->view('errors/404', ['title' => 'Categoría no encontrada']);
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();

            $nombre = trim($_POST['nombre'] ?? '');
            $slug   = trim($_POST['slug'] ?? '');

            if ($nombre === '') {
                $error = 'El nombre de la categoría es obligatorio.';
            } elseif ($slug !== '' && !preg_match('/^[a-z0-9-]+$/', $slug)) {
                $error = 'El slug solo puede contener letras minúsculas, números y guiones.';
            } else {
                $normalizedSlug   = $slug !== '' ? $slug : null;
                $existingCategory = $normalizedSlug ? Categoria::findBySlug($normalizedSlug) : null;

                if ($existingCategory && ((int) $existingCategory['id']) !== (int) ($id ?? 0)) {
                    $error = 'Ya existe una categoría con ese slug.';
                } else {
                    if ($id !== null) {
                        Categoria::update((int) $id, $nombre, $normalizedSlug);
                    } else {
                        Categoria::create($nombre, $normalizedSlug);
                    }
                    header('Location: ' . site_url('admin/categorias'));
                    exit;
                }
            }

            $category = [
                'id'     => $id !== null ? (int) $id : null,
                'nombre' => $nombre,
                'slug'   => $slug,
            ];
        }

        $this->view('admin/categories/form', [
            'title'    => $id ? 'Editar Categoría' : 'Nueva Categoría',
            'category' => $category,
            'error'    => $error,
        ]);
    }

    public function categoriaEliminar(string $id): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort(true);

        $id    = (int) $id;
        $count = Categoria::countProducts($id);

        if ($count > 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se puede eliminar. Hay ' . $count . ' productos asignados a esta categoría.',
            ]);
            exit;
        }

        Categoria::softDelete($id);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // NOTE: deleteProduct() was removed — it was dead code.
    // Product deletion is handled by AdminProductController::delete(),
    // which is the method actually routed in App.php.
}