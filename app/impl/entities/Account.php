<?php
/**
 * Account | Entity
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\entities\Permission;
use Brv\core\libs\database\Database as DB;
use Brv\impl\entities\Company;

/**
 * An entity representing an individual account or user.
 */
class Account extends Entity
{
    use common\CompanyId,
        common\StoreId;

    /** @var Company A Company singleton. */
    private $company = null;

    /**
     * Instantiate an account entity from a data row.
     *
     * @param array $row The data row from which to hydrate.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate an account entity by account id.
     *
     * @param integer $id The id of the account to query for.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("SELECT * FROM accounts WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
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
     * Factory method to instantiate an account entity by email address.
     *
     * @param string $email The email of the account to query for.
     * @return self
     */
    public static function queryEmail($email)
    {
        try {
            $stmt = DB::get()->prepare("SELECT * FROM accounts WHERE EmailAddress = :email LIMIT 1");
            $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
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
     * Commits Account to database.
     *
     * @return integer The id of the newly created/updated Account.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') !== -1) {
            try {
                $stmt = DB::get()->prepare("
                    UPDATE accounts
                    SET FirstName = :firstName,
                        LastName = :lastName,
                        EmailAddress = :email,
                        Password = :password,
                        CompanyID = :companyId,
                        StoreID = :storeId
                    WHERE id = :id
                ");
                $stmt->bindValue(':firstName', $this->getFirstName(), \PDO::PARAM_STR);
                $stmt->bindValue(':lastName', $this->getLastName(), \PDO::PARAM_STR);
                $stmt->bindValue(':email', $this->getEmailAddress(), \PDO::PARAM_STR);
                $stmt->bindValue(':password', $this->getPassword(), \PDO::PARAM_STR);
                $stmt->bindValue(':companyId', $this->getCompanyId(), \PDO::PARAM_INT);
                $stmt->bindValue(':storeId', $this->getStoreId(), \PDO::PARAM_INT);
                $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
                $stmt->execute();
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }
        } else {
            try {
                $stmt = DB::get()->prepare("
                    INSERT INTO accounts
                    SET DateCreated = NOW(),
                        FirstName = :firstName,
                        LastName = :lastName,
                        EmailAddress = :email,
                        Password = :password,
                        CompanyID = :companyId,
                        StoreID = :storeId
                ");
                $stmt->bindValue(':firstName', $this->getFirstName(), \PDO::PARAM_STR);
                $stmt->bindValue(':lastName', $this->getLastName(), \PDO::PARAM_STR);
                $stmt->bindValue(':email', $this->getEmailAddress(), \PDO::PARAM_STR);
                $stmt->bindValue(':password', $this->getPassword(), \PDO::PARAM_STR);
                $stmt->bindValue(':companyId', $this->getCompanyId(), \PDO::PARAM_INT);
                $stmt->bindValue(':storeId', $this->getStoreId(), \PDO::PARAM_INT);
                $stmt->execute();
                $this->set('id', DB::get()->lastInsertId());
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }
        }

        return null;
    }

    /**
     * Gets the account's password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->get('Password');
    }

    /**
     * Gets the first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->get('FirstName');
    }

    /**
     * Gets the last name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->get('LastName');
    }

    /**
     * Gets the email address.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->get('EmailAddress');
    }

    /**
     * Gets the legacy Permissions integer.
     *
     * @deprecated
     * @return integer
     */
    public function getLegacyPermissions()
    {
        return $this->get('Permissions');
    }

    /**
     * Gets the Permissions between an account and a target entity.
     *
     * @param  Entity $target The target entity the user is attempting to access.
     * @return Permission
     */
    public function getPermissions(Entity $target)
    {
        return new Permission($this->getId(), $target);
    }

    /**
     * Gets the company associated with the account.
     *
     * @return Company
     */
    public function getCompany()
    {
        if ($this->company === null) {
            $this->company = Company::queryAccount((int) $this->get('id'));
        }

        return $this->company;
    }
}
