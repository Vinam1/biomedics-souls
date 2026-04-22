<?php

class AdminProductController extends Controller
{
    private ProductService $productService;

    public function __construct()
    {
        $this->requireAdmin();
        $this->productService = new ProductService();
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $categoryId = intval($_GET['categoria'] ?? 0);
        $status = trim($_GET['estatus'] ?? '');
        $sort = trim($_GET['orden'] ?? 'updated_at_desc');

        $products = Producto::search([
            'query' => $search,
            'categoria_id' => $categoryId ?: null,
            'estatus' => $status ?: null,
            'sort' => $sort,
        ]);

        $categories = Categoria::all();

        $this->view('admin/products/index', [
            'title' => 'Productos - Admin',
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $categoryId,
            'selectedStatus' => $status,
            'selectedSort' => $sort,
        ]);
    }

    public function form(?string $id = null): void
    {
        $product = null;
        $error = null;
        $categories = Categoria::all();
        $formas = Forma::all();
        $etiquetasDisponibles = Etiqueta::all();
        $statusOptions = ['activo' => 'Activo', 'inactivo' => 'Inactivo', 'agotado' => 'Agotado'];

        if ($id !== null) {
            $product = $this->productService->getProductWithRelations((int) $id);
            if (!$product) {
                http_response_code(404);
                $this->view('errors/404', [
                    'title' => 'Producto no encontrado',
                ]);
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();

            $productData = $this->productService->prepareProductData($_POST);
            $relationIds = $this->productService->prepareRelationIds($_POST);

            $validationErrors = $this->productService->validateFormData($productData, $_FILES);
            if (!empty($validationErrors)) {
                $error = implode(' ', $validationErrors);
            } else {
                try {
                    $this->productService->saveProduct($productData, $relationIds, $_FILES, $id !== null ? (int) $id : null);
                    header('Location: ' . site_url('admin/productos'));
                    exit;
                } catch (RuntimeException $exception) {
                    $error = $exception->getMessage();
                }
            }

            $product = array_merge($productData, [
                'etiquetas' => $this->productService->buildRelationPayload($_POST['etiquetas'] ?? [], $etiquetasDisponibles),
                'images' => $id !== null && $product !== null
                    ? array_values(array_filter($product['images'] ?? [], static function ($image) use ($productData) {
                        return in_array((int) ($image['id'] ?? 0), $productData['existing_image_ids'] ?? [], true);
                    }))
                    : [],
            ]);
        }

        $this->view('admin/products/form', [
            'title' => $id !== null ? 'Editar producto' : 'Nuevo producto',
            'product' => $product,
            'categories' => $categories,
            'formas' => $formas,
            'etiquetasDisponibles' => $etiquetasDisponibles,
            'currentTags' => $product['etiquetas'] ?? [],
            'statusOptions' => $statusOptions,
            'error' => $error,
        ]);
    }

    public function delete(string $id): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        Producto::softDelete((int) $id);
        header('Location: ' . site_url('admin/productos'));
        exit;
    }
}
