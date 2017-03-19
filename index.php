<?php
/**
 * index.php
 *
 * The entry point to the application. All application flows are mapped
 * though this file. As the first script which runs, we establish
 * environment configurations.
 *
 * @version v0.0.1 (Mar. 17, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

/* Global dependencies. */
require_once '_index.php';
require_once 'app/App.php';

/* Check that we are not in an unsafe environment. */
if (DEBUG && $_SERVER['HTTP_HOST'] !== brv_host()) {
    die("Unsafe environment; host does not match. Application will abort.");
}

if (MAINTENANCE_MODE) {
    http_response_code(500);
    die("Sorry, we are currently down for maintenance. Please check back in 10 minutes.");
}

App::start();
?>
