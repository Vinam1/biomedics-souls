<?php

class AdminAttributesController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    // BUG FIX: index() now passes all variables the view needs
    public function index(): void
    {
        $this->view('admin/attributes/index', [
            'title'           => 'Gestión de Atributos',
            'etiquetasCount'  => Etiqueta::count(),
            'categoriesCount' => Categoria::count(),
            'productCount'    => Producto::countAll(),
        ]);
    }

    public function list(string $type): void
    {
        if (!AttributeService::isValidType($type)) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'No encontrado']);
            return;
        }

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $search  = trim($_GET['q'] ?? '');
        $perPage = 15;

        $data = AttributeService::getPaginatedData($type, $page, $search, $perPage);

        $this->view('admin/attributes/list', [
            'title'       => AttributeService::getTitle($type),
            'description' => AttributeService::getDescription($type),
            'type'        => $type,
            'items'       => $data['items'],
            'search'      => $data['search'],
            'currentPage' => $data['currentPage'],
            'totalPages'  => $data['totalPages'],
            'total'       => $data['total'],
        ]);
    }

    public function form(?string $type = null, ?string $id = null): void
    {
        if (!$type || !AttributeService::isValidType($type)) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'No encontrado']);
            return;
        }

        $modelClass = AttributeService::getModelClass($type);
        $item       = null;
        $error      = null;

        if ($id !== null) {
            $item = $modelClass::findById((int) $id);
            if (!$item) {
                http_response_code(404);
                $this->view('errors/404', ['title' => 'Atributo no encontrado']);
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();
            $errors = AttributeService::validateFormData($type, $_POST);

            if (!empty($errors)) {
                $error = implode('<br>', $errors);
            } else {
                $success = $this->handleSave($modelClass, $id, $type);

                if ($success) {
                    header('Location: ' . site_url("admin/atributos/$type"));
                    exit;
                }

                $error = 'Error al guardar. Intenta de nuevo.';
            }
        }

        $this->view('admin/attributes/form', [
            'title'  => $item
                ? 'Editar ' . AttributeService::getTitle($type)
                : 'Crear ' . AttributeService::getTitle($type),
            'type'   => $type,
            'item'   => $item,
            'error'  => $error,
            'isEdit' => $item !== null,
        ]);
    }

    public function delete(string $type, string $id): void
    {
        if (!AttributeService::isValidType($type)) {
            http_response_code(404);
            return;
        }

        $this->requirePost();
        $this->verifyCsrfOrAbort(true);

        $modelClass = AttributeService::getModelClass($type);
        $itemId     = (int) $id;
        $item       = $modelClass::findById($itemId);

        if (!$item) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Atributo no encontrado']);
            return;
        }

        $canDelete = AttributeService::canDelete($type, $itemId);
        if (!$canDelete['canDelete']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $canDelete['message']]);
            return;
        }

        $success = $modelClass::softDelete($itemId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Atributo eliminado correctamente' : 'Error al eliminar',
        ]);
    }

    public function getUpdated(string $type): void
    {
        if (!AttributeService::isValidType($type)) {
            http_response_code(404);
            return;
        }

        header('Content-Type: application/json');

        try {
            $modelClass = AttributeService::getModelClass($type);
            $items      = $modelClass::all();
            echo json_encode(['success' => true, 'items' => $items]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al obtener datos']);
        }
    }

    // BUG FIX: quickAdd now uses the model's create() which checks for duplicates,
    // preventing unique-constraint violations when the same name is submitted twice.
    public function quickAdd(string $type): void
    {
        if (!AttributeService::isValidType($type)) {
            http_response_code(404);
            return;
        }

        $this->requirePost();
        $this->verifyCsrfOrAbort(true);

        header('Content-Type: application/json');

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
            return;
        }

        try {
            $modelClass = AttributeService::getModelClass($type);
            $data       = AttributeService::prepareData($type, $_POST);

            // Use the model's create() method, which handles duplicate names gracefully
            $id = $modelClass::create($data);

            echo json_encode([
                'success' => true,
                'message' => 'Atributo creado',
                'id'      => $id,
                'nombre'  => $nombre,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al crear el atributo']);
        }
    }

    private function handleSave(string $modelClass, ?string $id, string $type): bool
    {
        $data = AttributeService::prepareData($type, $_POST);

        if ($id !== null) {
            return $modelClass::update((int) $id, $data);
        }

        // Use model's create() to respect duplicate-name logic
        return (bool) $modelClass::create($data);
    }
}