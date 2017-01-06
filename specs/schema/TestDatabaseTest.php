<?php
/**
 * TestDatabaseTest | TestCase
 *
 * @version v0.0.1 (Dec. 24, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

use PHPUnit\Framework\TestCase;

use Brv\core\libs\database\TestDatabase;
use Brv\core\libs\database\Database;

/**
 * Asserts that the TestDatabase class is working correctly.
 */
class TestDatabaseTest extends TestCase
{
    /**
     * Sets up database with base schema.
     * @beforeClass
     *
     * @throws \Exception if database cannot be setup.
     */
    public static function setUpBeforeClass() {
        $result = TestDatabase::executeFile(realpath(dirname(__FILE__) . '/schema.sql'));
        if ($result !== true) throw new \Exception("Failed to setup database for tests.");
    }

    /**
     * Truncates database.
     * @afterClass
     *
     * @throws \Exception if database cannot be truncated.
     */
    public static function tearDownAfterClass() {
        $result = TestDatabase::executeFile(realpath(dirname(__FILE__) . '/dropall.sql'));
        if ($result !== true) throw new \Exception("Failed to setup database for tests.");
    }

    /**
     * Tests a raw insert of data into the accounts table.
     * @test
     */
    public function RawInsertTest() {
        $email = "email@example.com";
        $password = "123";
        $id = -1;

        // Insert new row.
        $stmt = Database::get()->prepare("INSERT INTO accounts (EmailAddress, Password) VALUES (?, ?)");
        $this->assertNotFalse($stmt);
        $result = $stmt->execute([$email, $password]);
        $this->assertNotFalse($result);

        // Confirm id.
        $id = Database::get()->lastInsertId('id');
        $this->assertNotFalse($id); /* We need this to do strict type equality. */
        $this->assertGreaterThan(-1, $id);

        // Confirm row was inserted with correct data.
        $stmt = Database::get()->prepare("SELECT id, EmailAddress, Password FROM accounts WHERE EmailAddress = ?");
        $this->assertNotFalse($stmt);
        $result = $stmt->execute([$email]);
        $this->assertNotFalse($result);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertNotFalse($row);
        $this->assertEquals($id, $row['id']);
        $this->assertEquals($email, $row['EmailAddress']);
        $this->assertEquals($password, $row['Password']);
    }
}
?>
