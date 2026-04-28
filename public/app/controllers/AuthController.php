<?php

class AuthController extends Controller
{
    public function login(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();

            // Simple rate limiting
            $clientKey = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $attemptsKey = 'login_attempts_' . $clientKey;
            $lastAttemptKey = 'login_last_' . $clientKey;
            
            $attempts = $_SESSION[$attemptsKey] ?? 0;
            $lastAttempt = $_SESSION[$lastAttemptKey] ?? 0;
            
            if ($attempts >= 5 && time() - $lastAttempt < 900) { // 15 minutes
                $error = 'Demasiados intentos fallidos. Intenta nuevamente en 15 minutos.';
            } else {
                if (time() - $lastAttempt > 900) {
                    $attempts = 0; // Reset after 15 minutes
                }
                
                $_SESSION[$attemptsKey] = $attempts + 1;
                $_SESSION[$lastAttemptKey] = time();
            }

            if ($error) {
                // Skip further processing
            } else {
                $email = strtolower(trim($_POST['email'] ?? ''));
                $password = trim($_POST['password'] ?? '');

                if ($email === '' || $password === '') {
                    $error = 'Todos los campos son obligatorios.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Ingresa un correo electrónico válido.';
                } else {
                    $user = Usuario::findByEmail($email);
                    if ($user && password_verify($password, $user['password_hash'])) {
                        // Reset attempts on successful login
                        unset($_SESSION[$attemptsKey], $_SESSION[$lastAttemptKey]);
                        
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        if (in_array($user['role'], ['admin', 'superadmin'], true)) {
                            header('Location: ' . site_url('admin/dashboard'));
                        } else {
                            header('Location: ' . site_url('cuenta'));
                        }
                        exit;
                    } else {
                        $error = 'Credenciales incorrectas.';
                    }
                }
            }
        }

        $this->view('auth/login', [
            'title' => 'Iniciar sesión',
            'error' => $error,
        ]);
    }

    public function register(): void
    {
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrAbort();

            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = trim($_POST['password'] ?? '');

            if ($nombre === '' || $apellidos === '' || $email === '' || $password === '') {
                $error = 'Todos los campos son obligatorios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresa un correo electrónico válido.';
            } elseif (strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (Usuario::findByEmail($email)) {
                $error = 'Este correo ya está registrado.';
            } else {
                Usuario::create($nombre, $apellidos, $email, $password);
                $success = 'Registro exitoso. Ya puedes iniciar sesión.';
            }
        }

        $this->view('auth/register', [
            'title' => 'Registrar cuenta',
            'error' => $error,
            'success' => $success,
        ]);
    }

    public function logout(): void
    {
        $this->requirePost();
        $this->verifyCsrfOrAbort();

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        header('Location: ' . site_url());
        exit;
    }
}
