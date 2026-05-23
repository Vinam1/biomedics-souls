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

    public function resenas(): void
    {
        $this->view('admin/reviews/index', [
            'title' => 'Reseñas - Admin',
            'reviews' => Resena::allForAdmin(),
        ]);
    }

    public function clientes(): void
    {
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $totalClients = Usuario::countAdminClientList($filters);
        $totalPages = max(1, (int) ceil($totalClients / $perPage));
        $page = min($page, $totalPages);

        $this->view('admin/customers/index', [
            'title' => 'Clientes - Admin',
            'clients' => Usuario::adminClientList($filters, $page, $perPage),
            'filters' => $filters,
            'page' => $page,
            'perPage' => $perPage,
            'totalClients' => $totalClients,
            'totalPages' => $totalPages,
            'statusOptions' => array_merge(['' => 'Todos', 'sin_pedidos' => 'Sin pedidos'], array_combine(Pedido::STATUS_OPTIONS, Pedido::STATUS_OPTIONS)),
        ]);
    }

    public function clienteDetalle(string $id): void
    {
        $client = Usuario::findClientByIdForAdmin((int) $id);
        if (!$client) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Cliente no encontrado']);
            return;
        }

        $orders = Pedido::findByClienteIdForAdmin((int) $id);
        foreach ($orders as &$order) {
            $order['items'] = Pedido::items((int) $order['id']);
            $order['transaction'] = PagoTransaccion::latestByPedidoId((int) $order['id']);
        }
        unset($order);

        $this->view('admin/customers/show', [
            'title' => 'Perfil de cliente - ' . trim(($client['nombre'] ?? '') . ' ' . ($client['apellidos'] ?? '')),
            'client' => $client,
            'addresses' => Direccion::allByClienteId((int) $id),
            'orders' => $orders,
            'reviews' => Resena::findByClientForAdmin((int) $id),
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

    public function pedidoTicket(string $id): void
    {
        $order = Pedido::findById((int) $id);

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

    public function pedidoEstatus(string $id): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        $order = Pedido::findById((int) $id);
        if (!$order) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Pedido no encontrado']);
            return;
        }

        $status = trim((string) ($_POST['estado_pedido'] ?? ''));
        $currentUser = $this->getCurrentUser();

        if (!Pedido::updateStatus((int) $id, $status, isset($currentUser['id']) ? (int) $currentUser['id'] : null)) {
            $_SESSION['error'] = 'No se pudo actualizar el estatus del pedido.';
        } else {
            $_SESSION['success'] = 'El estatus del pedido se actualizó correctamente.';
        }

        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($redirectTo === 'cliente' && !empty($order['cliente_id'])) {
            header('Location: ' . site_url('admin/cliente-detalle/' . (int) $order['cliente_id']));
            exit;
        }

        header('Location: ' . site_url('admin/pedido-detalle/' . (int) $id));
        exit;
    }

    public function resenaEstatus(string $id): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        $review = Resena::findById((int) $id);
        if (!$review) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Reseña no encontrada']);
            return;
        }

        $status = trim((string) ($_POST['estatus'] ?? ''));
        if (!Resena::updateStatus((int) $id, $status)) {
            $_SESSION['error'] = 'No se pudo actualizar el estatus de la reseña.';
            header('Location: ' . site_url('admin/resenas'));
            exit;
        }

        $_SESSION['success'] = $status === 'publicada'
            ? 'La reseña fue publicada nuevamente.'
            : 'La reseña fue ocultada del catálogo.';

        header('Location: ' . site_url('admin/resenas'));
        exit;
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
