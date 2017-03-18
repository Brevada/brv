<?php
/**
 * EventTest | TestCase
 *
 * @version v0.0.1 (Jan. 09, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

use PHPUnit\Framework\TestCase;

use Brv\core\libs\database\TestDatabase;
use Brv\core\libs\database\Database;

/**
 *
 */
class EventTest extends TestCase
{
    /**
     * Sets up database with base schema.
     * @beforeClass
     *
     * @throws \Exception if database cannot be setup.
     */
    public static function setUpBeforeClass()
    {
        $result = TestDatabase::executeFile(realpath(dirname(__FILE__) . '/../schema/schema.sql'));
        if ($result !== true) {
            throw new \Exception("Failed to setup database for tests.");
        }
    }

    /**
     * Truncates database.
     * @afterClass
     *
     * @throws \Exception if database cannot be truncated.
     */
    public static function tearDownAfterClass()
    {
        $result = TestDatabase::executeFile(realpath(dirname(__FILE__) . '/../schema/dropall.sql'));
        if ($result !== true) {
            throw new \Exception("Failed to setup database for tests.");
        }
    }
}
