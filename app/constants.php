<?php
/**
 * Global Constants
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

const DEBUG = true;
const APP_NAME = 'Brevada';

const HOST_TOKEN = '[HOST_TOKEN]';
const HOST = 'brevada.com';
const DEV_HOST = 'brevada.local';
const MAIN_HOST = 'beta.' . HOST_TOKEN;
const LEGACY_HOST = HOST_TOKEN;

const NAMESPACE_PREFIX = 'Brv\\';
const NAMESPACE_CORE_PREFIX = 'core\\';
const NAMESPACE_IMPL_PREFIX = 'impl\\';

const NAMESPACE_DIR = __DIR__ . '/';
const NAMESPACE_CORE_DIR = __DIR__ . '/core/';
const NAMESPACE_IMPL_DIR = __DIR__ . '/impl/';

const LOG_DIRECTORY = __DIR__ . '/log/';

const ROUTER_CONFIG_PATH = NAMESPACE_IMPL_DIR . 'routes/config.yaml';

require __DIR__ . '/constants/STATES.php';
require __DIR__ . '/constants/HTTP.php';

/**
 * brv_scheme Determines the URL scheme.
 * @return string The URL scheme used to access the application.
 */
function brv_scheme()
{
    return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
            $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
}

/**
 * brv_host Returns the HTTP_HOST.
 * @return string The HTTP HOST.
 */
function brv_host()
{
    return str_replace(HOST_TOKEN, DEBUG ? DEV_HOST : HOST, MAIN_HOST);
}

/**
 * brv_legacy_host Returns the "legacy" HTTP_HOST. Should differ by subdomain.
 * @deprecated Legacy should be phased out.
 * @return string The "legacy" HTTP HOST.
 */
function brv_legacy_host()
{
    return str_replace(HOST_TOKEN, DEBUG ? DEV_HOST : HOST, LEGACY_HOST);
}

/**
 * brv_url Returns the SCHEME + HOST
 * @return string SCHEME + HOST
 */
function brv_url()
{
    return brv_scheme() . brv_host();
}

/**
 * brv_url Returns the legacy SCHEME + HOST
 * @deprecated
 * @return string SCHEME + HOST
 */
function brv_legacy_url()
{
    return brv_scheme() . brv_legacy_host();
}
