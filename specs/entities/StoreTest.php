<?php
/**
 * StoreTest | TestCase
 *
 * @version v0.0.1 (Jan. 09, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

use PHPUnit\Framework\TestCase;

use Brv\core\libs\database\TestDatabase;
use Brv\core\libs\database\Database;

use Brv\impl\entities\Store;

require_once 'app/App.php';

/**
 *
 */
class StoreTest extends TestCase
{
    /**
     * Sets up database with base schema.
     * @beforeClass
     *
     * @throws \Exception if database cannot be setup.
     */
    public static function setUpBeforeClass()
    {
        $result = TestDatabase::executeFile(realpath(dirname(__FILE__) . '/../schema/schema.sql')) &&
                  TestDatabase::executeFile(realpath(dirname(__FILE__) . '/../schema/company.sql'));
        if ($result !== true) {
            throw new \Exception("Failed to setup database for tests.");
        }
    }

    /**
     * Tests store creation.
     * @test
     */
    public function testStoreCreate()
    {
        $store = new Store();
        $store->setName('McRonald');
        $store->setCompanyId(1);
        $store->setActive();
        $store->setURL('mcronald');

        $id = $store->commit();

        $this->assertNotNull($id, "Store insertion failed.");
    }

    /**
     * Tests store update.
     * @test
     */
    public function testUpdate()
    {
        $store = new Store();
        $store->setName('Storey Store');
        $store->setCompanyId(1);
        $store->setActive();
        $store->setURL('mcronald');

        $id = $store->commit();
        $this->assertNotNull($id, "Store insertion failed.");

        $newName = 'ABC';
        $store->setName($newName);
        $store->commit();
        
        $store_updated = Store::queryId($id);

        $this->assertEquals($newName, $store_updated->getName());
    }

    /**
     * Tests invalid company id.
     * @test
     */
    public function testInvalidCompany()
    {
        $store = new Store();
        $store->setName('ZEY');
        $store->setCompanyId(-1);
        $store->setActive();
        $store->setURL('abc');

        $id = $store->commit();

        $this->assertNull($id);
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
