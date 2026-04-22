<?php

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $data['currentUser'] = $this->getCurrentUser();
        $data['csrfToken']   = csrf_token();
        extract($data);
        $viewFile = APPROOT . '/views/' . $view . '.php';
        require APPROOT . '/views/layouts/main.php';
    }

    protected function getCurrentUser(): ?array
    {
        // BUG FIX: Use a sentinel value so we don't re-query the DB on every call
        // when the user is not logged in.  Previously, static $user = null meant
        // the "if ($user !== null)" guard was never hit for unauthenticated requests,
        // causing a DB SELECT on every single getCurrentUser() call in the request.
        static $resolved = false;
        static $user     = null;

        if ($resolved) {
            return $user;
        }

        $resolved = true;

        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $user = Usuario::findById((int) $_SESSION['user_id']);
        return $user;
    }

    protected function requireAdmin(): void
    {
        $user = $this->getCurrentUser();
        if (!$user || !in_array($user['role'], ['admin', 'superadmin'], true)) {
            header('Location: ' . site_url('auth/login'));
            exit;
        }
    }

    protected function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->renderError(405, 'Método no permitido', 'La operación solicitada requiere una petición POST.');
            exit;
        }
    }

    protected function verifyCsrfOrAbort(bool $expectsJson = false): void
    {
        if (verify_csrf()) {
            return;
        }

        if ($expectsJson) {
            http_response_code(419);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'La sesión de seguridad expiró. Recarga la página e inténtalo de nuevo.',
            ]);
            exit;
        }

        $this->renderError(419, 'Sesión expirada', 'La validación de seguridad falló. Recarga la página y vuelve a intentarlo.');
        exit;
    }

    protected function renderError(int $statusCode, string $title, string $message): void
    {
        http_response_code($statusCode);
        $this->view('errors/generic', [
            'title'   => $title,
            'message' => $message,
        ]);
    }
}