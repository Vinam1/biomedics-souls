<?php
require_once __DIR__ . '/app/config/config.php';
bootstrap_security();
send_security_headers();

require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/core/Controller.php';
require_once APPROOT . '/core/App.php';

// Autoload controllers, modelos, servicios y core
spl_autoload_register(function ($class) {
    $paths = [
        APPROOT . '/controllers/' . $class . '.php',
        APPROOT . '/models/' . $class . '.php',
        APPROOT . '/services/' . $class . '.php',
        APPROOT . '/core/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

new App();
