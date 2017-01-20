<?php
/**
 * CompanyTest | TestCase
 *
 * @version v0.0.1 (Jan. 09, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

use PHPUnit\Framework\TestCase;

use Brv\core\libs\database\TestDatabase;
use Brv\core\libs\database\Database;

use Brv\impl\entities\Company;

require_once 'app/App.php';

/**
 *
 */
class CompanyTest extends TestCase
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
     * Tests that the "set" data is not altered.
     * @test
     */
    public function testImmutableData()
    {
        $name = 'Test Company';
        $active = true;
        $website = 'www.example.com';
        $phone = '123456789';
        $expiry = time();

        $company = new Company();
        $company->setName($name);
        $company->setActive($active);
        $company->setWebsite($website);
        $company->setPhoneNumber($phone);
        $company->setExpiryDate($expiry);

        $this->assertEquals($name, $company->getName());
        $this->assertEquals($active, $company->isActive());
        $this->assertEquals($website, $company->getWebsite());
        $this->assertEquals($phone, $company->getPhoneNumber());
        $this->assertEquals($expiry, $company->getExpiryDate());
    }

    /**
     * Tests the creation of a company.
     * @test
     */
    public function testCreateCompany()
    {
        $name = 'Test Company';
        $active = true;
        $website = 'www.example.com';
        $phone = '123456789';
        $expiry = time();

        $company = new Company();
        $company->setName($name);
        $company->setActive($active);
        $company->setWebsite($website);
        $company->setPhoneNumber($phone);
        $company->setExpiryDate($expiry);
        $id = $company->commit();

        $this->assertNotNull($id, "Company insertion failed.");
    }

    /**
     * Tests that the data persists in the database and can be recalled.
     * @test
     */
    public function testPersistantData()
    {
        $name = 'Test Company';
        $active = true;
        $website = 'www.example.com';
        $phone = '123456789';
        $expiry = time();

        $company = new Company();
        $company->setName($name);
        $company->setActive($active);
        $company->setWebsite($website);
        $company->setPhoneNumber($phone);
        $company->setExpiryDate($expiry);
        $id = $company->commit();

        $recalled = Company::queryId($id);

        $this->assertEquals($name, $recalled->getName());
        $this->assertEquals($active, $recalled->isActive());
        $this->assertEquals($website, $recalled->getWebsite());
        $this->assertEquals($phone, $recalled->getPhoneNumber());
        $this->assertEquals($expiry, $recalled->getExpiryDate());
        $this->assertTrue(abs($expiry - $recalled->getDateCreated()) < 100);
    }

    /**
     * Tests update.
     * @test
     */
    public function testUpdate()
    {
        $name = 'Test Company';
        $active = true;
        $website = 'www.example.com';
        $phone = '123456789';
        $expiry = time();

        // INSERT
        $company = new Company();
        $company->setName($name);
        $company->setActive($active);
        $company->setWebsite($website);
        $company->setPhoneNumber($phone);
        $company->setExpiryDate($expiry);
        $id = $company->commit();

        // UPDATE
        $newName = 'New Name';
        $company->setName($newName);
        $updatedId = $company->commit();

        $this->assertEquals($id, $updatedId);

        // Refresh
        $company = Company::queryId($company->getId());

        $this->assertEquals($newName, $company->getName());
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
