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
    use common\ValueTypes;

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
            $stmt = DB::get()->prepare("
                INSERT INTO aspect_type
                SET Title = :title, CompanyID = :companyId, ValueTypes = :valueTypes
            ");
            $stmt->bindValue(':title', $this->getTitle(), \PDO::PARAM_STR);
            $stmt->bindValue(':companyId', $this->get('CompanyID'), \PDO::PARAM_INT);
            $stmt->bindValue(':valueTypes', $this->get('ValueTypes'), \PDO::PARAM_STR);
            $stmt->execute();
            $this->set('id', (int) DB::get()->lastInsertId());
            return $this->getId();
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
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
    public function isCustom()
    {
        return ((int) $this->get('custom')) === 1;
    }

    /**
     * Adds a value type to the aspect type.
     *
     * @param string $key   The value key.
     * @param string $value The value label.
     */
    public function addValueType($key, $value)
    {
        $values = $this->getValueTypes();
        if ($values === null) $values = [];

        $values[$key] = $value;
        $this->set('ValueTypes', json_encode($values, true));
    }

    /**
     * Removes a value type from the aspect type.
     *
     * @param string $key   The value key.
     */
    public function removeValueType($key)
    {
        $values = $this->getValueTypes();
        if ($values === null) $values = [];
        if (isset($values[$key])) unset($values[$key]);
        if (empty($values)) {
            $this->set('ValueTypes', null);
        } else {
            $this->set('ValueTypes', json_encode($values, true));
        }
    }
}
