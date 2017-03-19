<?php
/**
 * Database | DAO
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\libs\database;

/**
 * The Database management class, or DAO, in which all database
 * access goes through.
 */
class Database
{
    /** @var \PDO A single PDO instance to persist a single connection. */
    private static $connection = null;

    /**
     * Factory method to instantiate an instance of the \PDO object,
     * or return one if it already exists.
     *
     * @return \PDO An instantiated \PDO connection to use.
     */
    public static function get()
    {
        if (empty(self::$connection)) {
            try {
                $target = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_SCHEMA);
                self::$connection = new \PDO($target, DB_USERNAME, DB_PASSWORD);
                self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
            } catch (\PDOException $ex) {
                \App::log()->error('PDOException: ' . $ex->getMessage());
                http_response_code(500);
                die("Sorry, we are having difficulty connecting to our system at the moment. Our technical team has been notified.");
            }
        }

        return self::$connection;
    }

    /**
     * Sets the PDO connection. Only valid in DEBUG environment.
     *
     * @throws \Exception if not in a DEBUG environment.
     * @param \PDO $connection The \PDO connection to set.
     * @return \PDO The new connection.
     */
    public static function set(\PDO $connection)
    {
        if (!DEBUG) {
            throw new \Exception("DEBUG environment required!");
        }
        return self::$connection = $connection;
    }
}
