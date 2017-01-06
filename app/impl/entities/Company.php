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
            $stmt = DB::get()->prepare("SELECT * FROM companies WHERE id = :id");
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
                SELECT companies.* FROM companies
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

    /**
     * Gets the company id.
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->get('id');
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
