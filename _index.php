<?php
/**
 * _index | Global Dependencies
 *
 * This script must be executed before every other script in the application,
 * as it contains global constants, functions, autoloading, error handling,
 * and environment configurations.
 *
 * @version v0.0.1 (Mar. 18, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

/* Require constants first so that we may use them in PHP env settings. */
require_once 'app/constants.php';
require_once 'app/config.php';

/* PHP Environment Variables */
date_default_timezone_set('America/New_York');
ini_set('display_errors', DEBUG ? '1' : '0');

/* Autoloading and Error Handling */
require_once 'app/registers.php';

/* Backwards compatibility with some older PHP versions. */
require_once 'app/backwards.php';

/* Composer Autoloader */
require_once 'vendor/autoload.php';
?>
