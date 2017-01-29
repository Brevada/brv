<?php
/**
 * Store | Entity
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;

/**
 * An entity representing a single store.
 */
class Store extends Entity
{
    /**
     * Instantiates a store entity from a data row.
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
     * @param integer $id The store id.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("SELECT * FROM stores WHERE id = :id");
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
     * Factory method to instantiate a store entity from an account.
     *
     * This chooses a default store belonging to the account.
     *
     * @param integer $id The account id.
     * @return self
     */
    public static function queryDefault($accountId)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    stores.*, UNIX_TIMESTAMP(stores.DateCreated) as sDateCreated
                FROM stores
                JOIN accounts ON accounts.CompanyID = stores.CompanyID
                WHERE accounts.StoreID = stores.id AND accounts.id = :id
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
     * Commits Store to database.
     *
     * @return integer The id of the newly created/updated Store.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') !== -1) {
            /*
             * UPDATE rather than INSERT.
             */
             try {
                 $stmt = DB::get()->prepare("
                     UPDATE locations SET
                     Country = :country, Province = :province, City = :city,
                     PostalCode = :postalcode, Longitude = :longitude, Latitude = :latitude
                     WHERE id = :id
                 ");
                 $stmt->bindValue(':id', $this->getLocationId(), \PDO::PARAM_INT);
                 $stmt->bindValue(':country', $this->getCountry(), \PDO::PARAM_STR);
                 $stmt->bindValue(':province', $this->getProvince(), \PDO::PARAM_STR);
                 $stmt->bindValue(':city', $this->getCity(), \PDO::PARAM_STR);
                 $stmt->bindValue(':postalcode', $this->getPostalCode(), \PDO::PARAM_STR);
                 $stmt->bindValue(':longitude', $this->getLongitude(), \PDO::PARAM_STR);
                 $stmt->bindValue(':latitude', $this->getLatitude(), \PDO::PARAM_STR);
                 $stmt->execute();
             } catch (\PDOException $ex) {
                 \App::log()->error($ex->getMessage());
             }

            try {
                $stmt = DB::get()->prepare("
                    UPDATE stores SET
                    `Name` = :name, `CompanyID` = :company_id,
                    `Active` = :active, `URLName` = :url,
                    `Website` = :website, `PhoneNumber` = :phone
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
                $stmt->bindValue(':name', $this->getName(), \PDO::PARAM_STR);
                $stmt->bindValue(':active', (int) $this->isActive(), \PDO::PARAM_INT);
                $stmt->bindValue(':website', $this->getWebsite(), \PDO::PARAM_STR);
                $stmt->bindValue(':phone', $this->getPhoneNumber(), \PDO::PARAM_STR);
                $stmt->bindValue(':url', $this->getURL(), \PDO::PARAM_STR);
                $stmt->bindValue(':company_id', $this->getCompanyId(), \PDO::PARAM_INT);
                $stmt->execute();
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }

            return null;
        } else {
            /* Completely new store. INSERT. */
            try {
                DB::get()->beginTransaction();

                // create Store location entry.
                $stmt = DB::get()->prepare("
                    INSERT INTO locations
                    (Country, Province, City, PostalCode, Longitude, Latitude)
                    VALUES (:country, :province, :city, :postalcode, :longitude, :latitude)
                ");
                $stmt->bindValue(':country', $this->getCountry(), \PDO::PARAM_STR);
                $stmt->bindValue(':province', $this->getProvince(), \PDO::PARAM_STR);
                $stmt->bindValue(':city', $this->getCity(), \PDO::PARAM_STR);
                $stmt->bindValue(':postalcode', $this->getPostalCode(), \PDO::PARAM_STR);
                $stmt->bindValue(':longitude', $this->getLongitude(), \PDO::PARAM_STR);
                $stmt->bindValue(':latitude', $this->getLatitude(), \PDO::PARAM_STR);
                $stmt->execute();
                $this->setLocationId(DB::get()->lastInsertId());

                // create Store Features entry.
                $stmt = DB::get()->prepare("INSERT INTO store_features VALUES ()");
                $stmt->execute();
                $this->setFeaturesId(DB::get()->lastInsertId());

                $this->set('sDateCreated', time());

                // Create store and link to features.
                $stmt = DB::get()->prepare("
                    INSERT INTO stores SET
                    `Name` = :name, `CompanyID` = :company_id,
                    `Active` = :active, `URLName` = :url,
                    `Website` = :website, `PhoneNumber` = :phone,
                    `LocationID` = :location_id, `FeaturesID` = :features_id,
                    `DateCreated` = FROM_UNIXTIME(:now)
                ");
                $stmt->bindValue(':name', $this->getName(), \PDO::PARAM_STR);
                $stmt->bindValue(':active', (int) $this->isActive(), \PDO::PARAM_INT);
                $stmt->bindValue(':website', $this->getWebsite(), \PDO::PARAM_STR);
                $stmt->bindValue(':phone', $this->getPhoneNumber(), \PDO::PARAM_STR);
                $stmt->bindValue(':url', $this->getURL(), \PDO::PARAM_STR);
                $stmt->bindValue(':company_id', $this->getCompanyId(), \PDO::PARAM_INT);
                $stmt->bindValue(':location_id', $this->getLocationId(), \PDO::PARAM_INT);
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
     * Gets the store id.
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->get('id');
    }

    /**
     * Gets the date the company created the "store".
     *
     * @return integer The date created in unix seconds.
     */
    public function getDateCreated()
    {
        return (int) $this->get('sDateCreated');
    }

    /**
     * Gets the company id.
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return (int) $this->get('CompanyID');
    }

    /**
     * Sets the company id.
     * @param integer $id
     */
    public function setCompanyId($id)
    {
        $this->set('CompanyID', (int) $id);
        return $this->getCompanyId();
    }

    /**
     * Gets the store's location id.
     *
     * @return integer
     */
    public function getLocationId()
    {
        return (int) $this->get('LocationID');
    }

    /**
     * Sets the store's location id.
     * @param integer $id
     */
    protected function setLocationId($id)
    {
        $this->set('LocationID', (int) $id);
        return $this->getLocationId();
    }

    /**
     * Gets the store's features id.
     *
     * @return integer
     */
    public function getFeaturesId()
    {
        return (int) $this->get('FeaturesID');
    }

    /**
     * Sets the store's features id.
     * @param integer $id
     */
    protected function setFeaturesId($id)
    {
        $this->set('FeaturesID', (int) $id);
        return $this->getFeaturesId();
    }

    /**
     * Gets the store's phone number.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->get('PhoneNumber');
    }

    /**
     * Sets the store's phone number.
     *
     * @param string $phone
     * @return string
     */
    public function setPhoneNumber($phone)
    {
        $this->set('PhoneNumber', $phone);
        return $this->getPhoneNumber();
    }

    /**
     * Gets the store name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('Name');
    }

    /**
     * Sets the store name.
     *
     * @param string $name
     * @return string
     */
    public function setName($name)
    {
        $this->set('Name', $name);
        return $this->getName();
    }

    /**
     * Checks if the store is active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->get('Active') == 1;
    }

    /**
     * Sets the store's active state.
     *
     * @param boolean $state The new state.
     * @return boolean
     */
    public function setActive($state = true)
    {
        $this->set('Active', (int) $state);
        return $this->isActive();
    }

    /**
     * Gets the store URL.
     *
     * @return string
     */
    public function getURL()
    {
        return $this->get('URLName');
    }

    /**
     * Sets the store URL.
     *
     * @param string $url
     * @return string
     */
    public function setURL($url)
    {
        $this->set('URLName', $url);
        return $this->getURL();
    }

    /**
     * Gets the store website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->get('Website');
    }

    /**
     * Sets the store website.
     *
     * @param string $website
     * @return string
     */
    public function setWebsite($website)
    {
        $this->set('Website', $website);
        return $this->getWebsite();
    }
}
