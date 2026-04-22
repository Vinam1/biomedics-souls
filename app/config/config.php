<?php

// Configuración de la base de datos y constantes de la aplicación
define('DB_HOST', 'localhost');
define('DB_NAME', 'biomedics_souls');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APPROOT', dirname(__DIR__));

define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

function site_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL === '/' ? '/' . $path : BASE_URL . '/' . $path;
}

function asset_url(string $path = ''): string
{
    return site_url('assets/' . ltrim($path, '/'));
}

function bootstrap_security(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) == 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function send_security_headers(): void
{
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cross-Origin-Opener-Policy: same-origin');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(?string $token = null): bool
{
    $token ??= $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $sessionToken = $_SESSION['csrf_token'] ?? null;

    return is_string($token)
        && is_string($sessionToken)
        && hash_equals($sessionToken, $token);
}
