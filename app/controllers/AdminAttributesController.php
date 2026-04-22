<?php

class AdminAttributesController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $attributeTypes = [
            'etiquetas' => [
                'name' => 'Etiquetas',
                'description' => 'Etiquetas para clasificar productos',
                'count' => Etiqueta::count(),
                'icon' => 'fa-tags'
            ]
        ];

        $this->view('admin/attributes/index', [
            'title' => 'Gestión de Atributos',
            'attributeTypes' => $attributeTypes
        ]);
    }

    public function list(string $type): void
    {
        if (!AttributeService::isValidType($type)) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'No encontrado']);
            return;
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['q'] ?? '');
        $perPage = 15;

        $data = AttributeService::getPaginatedData($type, $page, $search, $perPage);

        $this->view('admin/attributes/list', [
            'title' => AttributeService::getTitle($type),
            'description' => AttributeService::getDescription($type),
            'type' => $type,
            'items' => $data['items'],
            'search' => $data['search'],
            'currentPage' => $data['currentPage'],
            'totalPages' => $data['totalPages'],
            'total' => $data['total'],
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
        $item = null;
        $error = null;

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
            'title' => $item ? 'Editar ' . AttributeService::getTitle($type) : 'Crear ' . AttributeService::getTitle($type),
            'type' => $type,
            'item' => $item,
            'error' => $error,
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
        $itemId = (int) $id;
        $item = $modelClass::findById($itemId);
        if (!$item) {
            http_response_code(404);
            return;
        }

        $canDelete = AttributeService::canDelete($type, $itemId);
        if (!$canDelete['canDelete']) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $canDelete['message']
            ]);
            return;
        }

        $success = $modelClass::softDelete($itemId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Atributo eliminado correctamente' : 'Error al eliminar'
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
            $items = $modelClass::all();
            echo json_encode(['success' => true, 'items' => $items]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al obtener datos']);
        }
    }

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
        $modelClass = AttributeService::getModelClass($type);

        if ($nombre === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
            return;
        }

        try {
            $data = AttributeService::prepareData($type, $_POST);
            $id = $this->createAttribute($modelClass, $data);

            echo json_encode([
                'success' => true,
                'message' => 'Atributo creado',
                'id' => $id,
                'nombre' => $nombre
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al crear']);
        }
    }

    private function handleSave(string $modelClass, ?string $id, string $type): bool
    {
        $data = AttributeService::prepareData($type, $_POST);

        if ($id !== null) {
            return $modelClass::update((int) $id, $data);
        }

        return (bool) $this->createAttribute($modelClass, $data);
    }

    private function createAttribute(string $modelClass, array $data): int
    {
        $db = Database::getInstance();
        $slug = $data['slug'] ?? AttributeService::generateSlug($data['nombre']);

        $columns = ['nombre', 'slug'];
        $values = [':nombre', ':slug'];
        $bindings = ['nombre' => $data['nombre'], 'slug' => $slug];

        foreach (['color', 'descripcion', 'icono'] as $field) {
            if (isset($data[$field])) {
                $columns[] = $field;
                $values[] = ':' . $field;
                $bindings[$field] = $data[$field];
            }
        }

        $table = str_replace(['Etiqueta', 'Uso', 'Beneficio'], ['etiquetas', 'usos', 'beneficios'], $modelClass);

        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';
        $stmt = $db->prepare($sql);
        $stmt->execute($bindings);

        return (int) $db->lastInsertId();
    }
}
