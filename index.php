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
    exit("Unsafe environment; host does not match. Application will abort.");
}

App::start();
?>
