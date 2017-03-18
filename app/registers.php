<?php
/**
 * Registers | Global Event Handlers
 *
 * @version v0.0.1 (Jan. 2, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

// PSR-4 compliant autoloader.
spl_autoload_register(function ($class) {
    // Assert correct name space prefix.
    if (strncmp(NAMESPACE_PREFIX, $class, strlen(NAMESPACE_PREFIX)) !== 0) {
        return;
    }

    $relative = substr($class, strlen(NAMESPACE_PREFIX));

    $file = NAMESPACE_DIR . str_replace('\\', '/', $relative) . '.php';
    if (is_readable($file)) {
        require $file;
    }
});

// Detect bad shutdown.
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err !== null) {
        // Handle bad shutdown. SERVER: 500
    }
});

// Global error handler.
if (!DEBUG) {
    set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
        // Log error, SERVER: 500.
    }, E_ALL | E_STRICT);
}
