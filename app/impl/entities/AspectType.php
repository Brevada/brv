<?php
/**
 * AspectType | Entity
 *
 * @version v0.0.1 (Dec. 31, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;

/**
 * An entity representing a type of aspect.
 */
class AspectType extends Entity
{
    /**
     * Instantiates an aspect type entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate an aspect type entity from an aspect type id.
     *
     * @param integer $id The aspect id.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    aspect_type.*, NOT(ISNULL(aspect_type.CompanyID)) as custom
                FROM aspect_type
                WHERE aspect_type.id = :id
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

    /* Instance Methods */

    /**
     * Commits Aspect Type to database.
     *
     * @return integer The id of the newly created Aspect Type.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') != -1) {
            throw new \Exception('UPDATE has not been implemented.');
        }

        try {
            $stmt = DB::get()->prepare("INSERT INTO aspect_type SET Title = :title, CompanyID = :companyId");
            $stmt->bindValue(':title', $this->getTitle(), \PDO::PARAM_STR);
            $stmt->bindValue(':companyId', $this->get('CompanyID'), \PDO::PARAM_INT);
            $stmt->execute();
            $this->set('id', (int) DB::get()->lastInsertId());
            return $this->getId();
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Gets the aspect type id.
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->get('id');
    }

    /**
     * Gets the aspect type title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('Title');
    }

    /**
     * Checks if the aspect type is custom to a company.
     *
     * @return boolean
     */
    public function getCustom()
    {
        return ((int) $this->get('custom')) === 1;
    }
}
