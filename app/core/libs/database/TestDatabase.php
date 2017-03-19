<?php
/**
 * Test Database | Testing
 *
 * @version v0.0.1 (Dec. 23, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\libs\database;

use Brv\core\libs\database\Database;

/**
 * A test Database management class, or DAO, in which all database
 * access goes through in a testing environment.
 */
class TestDatabase
{
    /** @var boolean Indicates whether the DEBUG environment has been setup. */
    private static $setup = false;

    /**
     * Asserts that the script is being executed in a DEBUG environment.
     *
     * @throws \Exception If not in DEBUG environment.
     * @return array[] The connection details of the valid environment.
     */
    private static function assertValidEnvironment()
    {
        if (!DEBUG) {
            throw new \Exception("DEBUG environment required!");
        }

        $host = getenv('BRV_DEV_DB_HOST');
        $schema = getenv('BRV_DEV_DB_SCHEMA');
        $username = getenv('BRV_DEV_DB_USERNAME');
        $password = getenv('BRV_DEV_DB_PASSWORD');

        if (empty($host) || empty($schema) || empty($username)) {
            throw new \Exception("DEBUG database configuration must be set!");
        }

        if ($password === false) {
            $password = '';
        }

        if ($host == DB_HOST && $schema == DB_SCHEMA) {
            throw new \Exception("DEBUG database configuration must be different than production!");
        }

        return [
            "host" => $host,
            "schema" => $schema,
            "username" => $username,
            "password" => $password
        ];
    }

    /**
     * Sets up an alternative database connection for testing purposes.
     *
     * Only available in DEBUG environment.
     *
     * @throws \Exception If not in DEBUG environment.
     * @return \PDO The debug \PDO instance.
     */
    public static function debugInit()
    {
        $cnx = self::assertValidEnvironment();
        if (self::$setup) {
            return Database::get();
        }

        try {
            $target = sprintf("mysql:host=%s;dbname=%s", $cnx['host'], $cnx['schema']);
            $res = Database::set(new \PDO($target, $cnx['username'], $cnx['password']));
            $res->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $res->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
            self::$setup = true;
            return $res;
        } catch (\PDOException $ex) {
            \App::log()->error('PDOException: ' . $ex->getMessage());
        }

        return null;
    }

    /**
     * Executes a SQL file.
     *
     * @throws \Exception If not in DEBUG environment or path does not exist.
     * @param string $path The file path of the SQL file.
     * @return boolean|string True on success or an error message on failure.
     */
    public static function executeFile($path)
    {
        $cnx = self::assertValidEnvironment();
        if (!self::$setup) {
            self::debugInit();
        }

        if (!file_exists($path)) {
            throw new \Exception("Invalid SQL file path.");
        }

        $out = [];
        $cmd = "mysql -u{$cnx['username']} -p{$cnx['password']} -h {$cnx['host']} -D {$cnx['schema']}";
        exec($cmd . ' < ' . escapeshellarg($path) . ' 2>&1', $out, $exitCode);

        if ($exitCode == 0 || empty($out)) {
            return true;
        } else {
            return end($out);
        }
    }
}
