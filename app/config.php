<?php
/**
 * Configuration Loader
 *
 * @version v0.0.1 (Mar. 18, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

function load_config() {
    if (getenv('BRV_CONFIG_PATH') === false) {
        die("Missing Brevada configuration file.");
    }

    /* Load application configuration. */
    $config = parse_ini_file(getenv('BRV_CONFIG_PATH'), false);
    if ($config === false) {
        die("Invalid configuration for the Brevada system.");
    }

    $defaults = [
        "debug" => false,
        "maintenance_mode" => (bool) getenv('BRV_MAINTENANCE_MODE'),

        "host" => null,
        "dev_host" => null,
        "main_host" => null,
        "legacy_host" => null,

        "db_username" => "root",
        "db_password" => "",
        "db_host" => "localhost",
        "db_schema" => null,

        "feedback_version" => null,

        "log_directory" => __DIR__ . '/log/'
    ];

    foreach (array_keys($defaults) as $key) {
        if (isset($config[$key]) && !is_null($config[$key])) {
            $defaults[$key] = $config[$key];
        }

        define(strtoupper($key), $defaults[$key]);
    }

}

load_config();
?>
