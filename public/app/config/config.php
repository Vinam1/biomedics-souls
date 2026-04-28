<?php

// Cargar variables de entorno desde .env
if (file_exists(__DIR__ . '/../../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Configuración de la base de datos y constantes de la aplicación
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'amiptcnl_biomedics_souls');
define('DB_USER', getenv('DB_USER') ?: 'amiptcnl_biomedics');
define('DB_PASS', getenv('DB_PASS') ?: 'biomedics123456');

if (!getenv('DB_HOST') || !getenv('DB_NAME') || !getenv('DB_USER') || !getenv('DB_PASS')) {
    error_log('WARNING: Using hardcoded database credentials. Ensure .env file is properly configured.');
}

define('APPROOT', dirname(__DIR__));

define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Deshabilitar reporte de errores en producción
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

function site_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = BASE_URL === '/' ? '/' : BASE_URL . '/';
    return $base . 'index.php?url=' . $path;
}

function asset_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL === '/' ? '/assets/' . $path : BASE_URL . '/assets/' . $path;
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
    
    // Add HSTS for HTTPS enforcement
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    
    // Basic CSP
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; img-src \'self\' data: https:;');
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
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    }
    $sessionToken = $_SESSION['csrf_token'] ?? null;

    return is_string($token)
        && is_string($sessionToken)
        && hash_equals($sessionToken, $token);
}
