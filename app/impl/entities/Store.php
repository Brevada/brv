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
                SELECT stores.* FROM stores
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
     * Gets the store id.
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->get('id');
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
     * Gets the store's phone number.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->get('PhoneNumber');
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
     * Checks if the store is active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->get('Active') == 1;
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
     * Gets the store website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->get('Website');
    }
}
