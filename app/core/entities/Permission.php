<?php
/**
 * Permission
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\entities;

use Brv\core\entities\Entity;

use Brv\impl\entities\Aspect;
use Brv\impl\entities\Store;
use Brv\impl\entities\Company;
use Brv\impl\entities\Event;
use Brv\impl\entities\Account;

/**
 * Permission
 *
 * Describes the permissions a user has in regards to the user's
 * interaction with a particular resource.
 *
 * @todo Not fully implemented.
 */
class Permission
{
    /**#@+
     * Permission flags which can be combined to indicate different
     * levels of permissions.
     */
    /** @var integer Default state flag of no access. Cannot be combined. */
    const NO_ACCESS = 0;

    /** @var integer Indicates user can read resource. */
    const CAN_READ = 0b1;

    /** @var integer Indicates user can write to resource. */
    const CAN_WRITE = 0b10;

    /** @var integer Indicates user can grant/remove permissions for the resource. */
    const CAN_ASSIGN = 0b100;
    /**#@-*/

    /** @var int Combined permission flags. */
    private $permissions = 0;

    /**
     * Instantiate a new Permission object describing the relationship between
     * the account and the target resource.
     *
     * @todo Must implement connection to permissions in schema.
     * @param integer $accountId The ID of the user attempting to interact with the resource.
     * @param Entity $target The target resource with which to interact.
     */
    public function __construct($accountId, Entity $target)
    {
        try {
            if ($target instanceof Aspect) {
                /* Aspect access is synonymous with store access in this version. */
                $this->hydrate(new self($accountId, Store::queryId($target->getStoreId())));
            } elseif ($target instanceof Event) {
                /* Event access is synonymous with store access in this version. */
                $this->hydrate(new self($accountId, Store::queryId($target->getStoreId())));
            } elseif ($target instanceof Company) {
                $account = Account::queryId($accountId);
                if ($account->getCompanyId() == $target->getId()) {
                    $this->permissions = self::CAN_READ | self::CAN_WRITE | self::CAN_ASSIGN;
                }
            } elseif ($target instanceof Store) {
                $account = Account::queryId($accountId);
                if ($account->getStoreId() == $target->getId()) {
                    $this->permissions = self::CAN_READ | self::CAN_WRITE;

                    if ($account->getCompanyId() == $target->getCompanyId()) {
                        $this->permissions |= self::CAN_ASSIGN;
                    }
                }
            } else {
                $this->permissions = self::NO_ACCESS;
            }
        } catch (\Exception $ex) {
            \App::log()->error($ex->getMessage());
            $this->permissions = self::NO_ACCESS;
        }
    }

    /**
     * Hydrates the current instance using another Permission instance.
     * @param  self $p The Permission to hydrate from.
     * @return self
     */
    protected function hydrate(Permission $p)
    {
        $this->permissions = $p->getRawPermissions();
        return $this;
    }

    /**
     * Gets the raw permission value.
     * @return integer
     */
    public function getRawPermissions()
    {
        return $this->permissions;
    }

    /**
     * Tests if the user can read the resource.
     * @return boolean
     */
    public function canRead()
    {
        return $this->permissions & self::CAN_READ;
    }

    /**
     * Tests if the user can write to the resource.
     * @return boolean
     */
    public function canWrite()
    {
        return $this->permissions & self::CAN_WRITE;
    }

    /**
     * Tests if the user can modify permissions for the resource.
     * @return boolean
     */
    public function canAssign()
    {
        return $this->permissions & self::CAN_ASSIGN;
    }
}
