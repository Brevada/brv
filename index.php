<?php
/**
 * index.php
 *
 * The entry point to the application. All application flows are mapped
 * though this file. As the first script which runs, we establish
 * environment configurations.
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

/* Global dependencies. */
require_once '_index.php';

require_once 'app/App.php';

App::start();
?>
