<?php
/**
 * Company | Entity
 *
 * @version v0.0.1 (Dec. 30, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;
use Brv\impl\entities\Industry;

/**
 * An entity representing a company.
 */
class Company extends Entity
{
    use common\FeaturesId,
        common\Contact,
        common\Active,
        common\CompanyFeatures;

    /** @var Industry A Industry singleton. */
    private $industry = null;

    /**
     * Instantiates a company entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate a store entity from a store id.
     *
     * @param integer $id The company id.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
            SELECT companies.*,
                UNIX_TIMESTAMP(companies.DateCreated) AS sDateCreated,
                UNIX_TIMESTAMP(companies.ExpiryDate) AS sDateExpiry,
                company_features.MaxTablets, company_features.MaxStores,
                company_features.MaxAccounts
            FROM companies
            LEFT JOIN company_features ON company_features.id = companies.FeaturesID
            WHERE companies.id = :id
            LIMIT 1
            ");
            $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from($row);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Factory method to instantiate a company entity from an account.
     *
     * @param integer $id The account id.
     * @return self
     */
    public static function queryAccount($accountId)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT companies.*,
                    UNIX_TIMESTAMP(companies.DateCreated) AS sDateCreated,
                    UNIX_TIMESTAMP(companies.ExpiryDate) AS sDateExpiry,
                    company_features.MaxTablets, company_features.MaxStores,
                    company_features.MaxAccounts
                FROM companies
                LEFT JOIN company_features ON company_features.id = companies.FeaturesID
                JOIN accounts ON accounts.CompanyID = companies.id
                WHERE accounts.id = :id
                LIMIT 1
            ");
            $stmt->bindValue(':id', (int) $accountId, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from($row);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /* Instance Methods */

    /**
     * Commits Company to database.
     *
     * @return integer The id of the newly created/updated Company.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') !== -1) {
            /*
             * UPDATE rather than INSERT.
             */
             try {
                 $stmt = DB::get()->prepare("
                     UPDATE company_features SET
                     MaxTablets = :maxTablets,
                     MaxAccounts = :maxAccounts,
                     MaxStores = :maxStores
                     WHERE id = :features_id
                 ");
                 $stmt->bindValue(':features_id', $this->getFeaturesId(), \PDO::PARAM_INT);
                 $stmt->bindValue(':maxTablets', $this->getMaxTablets(), \PDO::PARAM_INT);
                 $stmt->bindValue(':maxAccounts', $this->getMaxAccounts(), \PDO::PARAM_INT);
                 $stmt->bindValue(':maxStores', $this->getMaxStores(), \PDO::PARAM_INT);
                 $stmt->execute();
             } catch (\PDOException $ex) {
                 \App::log()->error($ex->getMessage());
             }

            try {
                $stmt = DB::get()->prepare("
                    UPDATE companies SET
                    Name = :name, Active = :active, Website = :website,
                    PhoneNumber = :phone, ExpiryDate = FROM_UNIXTIME(:expiry),
                    CategoryID = :category_id
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
                $stmt->bindValue(':name', $this->getName(), \PDO::PARAM_STR);
                $stmt->bindValue(':active', (int) $this->isActive(), \PDO::PARAM_INT);
                $stmt->bindValue(':website', $this->getWebsite(), \PDO::PARAM_STR);
                $stmt->bindValue(':phone', $this->getPhoneNumber(), \PDO::PARAM_STR);
                $stmt->bindValue(':expiry', $this->getExpiryDate(), \PDO::PARAM_INT);
                $stmt->bindValue(':category_id', $this->getCategoryId(), \PDO::PARAM_INT);
                $stmt->execute();
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }

            return null;
        } else {
            /* Completely new company. INSERT. */
            try {
                DB::get()->beginTransaction();

                // First create Company Features entry.
                $stmt = DB::get()->prepare("
                    INSERT INTO company_features SET
                    MaxTablets = :maxTablets, MaxAccounts = :maxAccounts,
                    MaxStores = :maxStores
                ");
                $stmt->bindValue(':maxTablets', $this->getMaxTablets(), \PDO::PARAM_INT);
                $stmt->bindValue(':maxAccounts', $this->getMaxAccounts(), \PDO::PARAM_INT);
                $stmt->bindValue(':maxStores', $this->getMaxStores(), \PDO::PARAM_INT);
                $stmt->execute();
                $this->set('FeaturesID', (int) DB::get()->lastInsertId());

                $this->set('sDateCreated', time());

                // Create company and link to features.
                $stmt = DB::get()->prepare("
                    INSERT INTO companies SET
                    Name = :name, Active = :active, Website = :website,
                    PhoneNumber = :phone, ExpiryDate = FROM_UNIXTIME(:expiry),
                    CategoryID = :category_id, FeaturesID = :features_id,
                    DateCreated = FROM_UNIXTIME(:now)
                ");
                $stmt->bindValue(':name', $this->getName(), \PDO::PARAM_STR);
                $stmt->bindValue(':active', (int) $this->isActive(), \PDO::PARAM_INT);
                $stmt->bindValue(':website', $this->getWebsite(), \PDO::PARAM_STR);
                $stmt->bindValue(':phone', $this->getPhoneNumber(), \PDO::PARAM_STR);
                $stmt->bindValue(':expiry', $this->getExpiryDate(), \PDO::PARAM_INT);
                $stmt->bindValue(':category_id', $this->getCategoryId(), \PDO::PARAM_INT);
                $stmt->bindValue(':features_id', $this->getFeaturesId(), \PDO::PARAM_INT);
                $stmt->bindValue(':now', $this->getDateCreated(), \PDO::PARAM_INT);
                $stmt->execute();
                $this->set('id', (int) DB::get()->lastInsertId());

                DB::get()->commit();

                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }

            try {
                DB::get()->rollBack();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }
        }

        return null;
    }

    /**
     * Gets the company's category id.
     *
     * @return integer
     */
    public function getCategoryId()
    {
        if ($this->get('CategoryID') === null) return null;
        return (int) $this->get('CategoryID');
    }

    /**
     * Sets the company's category id.
     *
     * @param integer $id The new category id.
     * @return integer
     */
    public function setCategoryId($id)
    {
        $this->set('CategoryID', (int) $id);
        return $this->getCategoryId();
    }

    /**
     * Gets the date the company created an "account" (the company entry in
     * the schema).
     *
     * @return integer The date created in unix seconds.
     */
    public function getDateCreated()
    {
        return (int) $this->get('sDateCreated');
    }

    /**
     * Sets the date the company's plan will expire.
     *
     * @param integer $seconds The unix timestamp to set the expiry date to or offset.
     * @param boolean $isOffset Indicates if the $seconds should be added to the
     * current time.
     *
     * @return integer The date in unix seconds.
     */
    public function setExpiryDate($seconds, $isOffset = false)
    {
        $this->set('sDateExpiry', ($isOffset ? time() : 0) + (int) $seconds);
        return $this->getExpiryDate();
    }

    /**
     * Gets the date the company's plan will expire.
     *
     * @return integer The date in unix seconds.
     */
    public function getExpiryDate()
    {
        return (int) $this->get('sDateExpiry');
    }

    /**
     * Gets the company's website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->get('Website');
    }

    /**
     * Sets the company's website.
     *
     * @param string $website The new website.
     * @return string
     */
    public function setWebsite($website)
    {
        $this->set('Website', $website);
        return $this->getWebsite();
    }

    /**
     * Gets the industry associated with the company.
     *
     * @return Industry
     */
    public function getIndustry()
    {
        if ($this->industry !== null) {
            return $this->industry;
        }
        return $this->industry = Industry::queryCompany((int) $this->get('id'));
    }
}
