<?php
/**
 * App | Main Entry
 *
 * @version v0.0.1 (Jan. 2, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Brv\core\routing\Router as Router;

/**
 * App
 *
 * Manages application flow (meta-routing).
 */
class App
{
    /** @var Logger stores a Logger singleton. */
    private static $logger;

    /** @var array Stores state information for current execution. */
    private static $state = [];

    /**
     * Begins the application flow.
     */
    public static function start()
    {
        session_name('brevada_session');
        session_set_cookie_params(0, '/', '.' . (DEBUG ? DEV_HOST : HOST));
        session_start();

        Router::execute();
    }

    /**
     * Redirects the client to a URL.
     * @param string $url The URL to redirect the client to.
     * @param  boolean $fullUri Indicates if the $url should be treated as a complete URI.
     */
    public static function redirect($url, $fullUri = false)
    {
        $dest = $fullUri ? $url : brv_url() . '/' . $url;
        $code = 303;

        if (isset($_SERVER['SERVER_PROTOCOL']) && strcasecmp($_SERVER['SERVER_PROTOCOL'], 'HTTP/1.0') === 0) {
            $code = 302;
        }

        // Allows use of "token" to represent the host name (helpful for dev
        // environment).
        $dest = str_replace(HOST_TOKEN, DEBUG ? DEV_HOST : HOST, $dest);

        header('Location: ' . $dest, true, $code);
        exit();
    }

    /**
     * Sets a state value with an associated key. Do not persist by default.
     *
     * @param string $key The key.
     * @param mixed $val The state value to store.
     * @param boolean $persist Determines if the data should be persisted past the current execution.
     * @return mixed The newly stored value.
     */
    public static function setState($key, $val, $persist = false)
    {
        if ($persist) {
            $_SESSION[$key] = $val;
        }
        return self::$state[$key] = $val;
    }

    /**
     * Gets a state value from a key.
     *
     * @param string $key The key.
     * @return string
     */
    public static function getState($key)
    {
        $value = isset(self::$state[$key]) ? self::$state[$key] : null;
        if ($value === null) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        } else {
            return $value;
        }
    }

    /**
     * Clears all state information, including data stored in persistant storage.
     */
    public static function clearState()
    {
        $_SESSION = [];
        self::$state = [];
    }

    /**
     * log Returns a newly instantiated logger instance.
     *
     * @return Logger Monolog logger.
     */
    public static function log()
    {
        if (empty(self::$logger)) {
            self::$logger = new Logger(APP_NAME);
            self::$logger->pushHandler(new StreamHandler(LOG_DIRECTORY . 'debug.log'), Logger::DEBUG);
        }

        return self::$logger;
    }
}
