<?php
/**
 * Response | Entity
 *
 * @version v0.0.1 (Dec. 22, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\data\IResponse;
use Brv\core\libs\database\Database as DB;

/**
 * An entity representing a single feedback response.
 */
class Response extends Entity implements IResponse
{
    /**
     * Instantiates a response entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate an array of Responses.
     *
     * @param integer $id Aspect Id
     * @param integer $from From date in seconds since epoch.
     * @param integer $to To date in seconds since epoch.
     * @return self[]
     */
    public static function queryAspect($id, $from = 0, $to = 0)
    {
        if ($to == 0) {
            $to = time();
        }

        $responses = [];

        try {
            $stmt = DB::get()->prepare("
                SELECT
                    feedback.id as _id, UNIX_TIMESTAMP(feedback.Date) as _date,
                    feedback.Rating as _value, aspects.AspectTypeID as _typeId
                FROM feedback
                JOIN aspects ON aspects.id = feedback.AspectID
                WHERE
                    feedback.AspectID = :id AND
                    feedback.Date BETWEEN FROM_UNIXTIME(:from) AND FROM_UNIXTIME(:to)
                ORDER BY feedback.Date ASC
            ");

            $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
            $stmt->bindValue(':from', (int) $from, \PDO::PARAM_INT);
            $stmt->bindValue(':to', (int) $to, \PDO::PARAM_INT);
            $stmt->execute();

            $responses = array_map(function ($row) {
                return self::from([])->hydrate($row, Entity::HYDRATE_UNDERSCORE);
            }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return $responses;
    }

    /**
     * Factory method to instantiate a response entity from a feedback id.
     *
     * @param integer $id The feedback id.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    feedback.id as _id, UNIX_TIMESTAMP(feedback.Date) as _date,
                    feedback.Rating as _value, aspects.AspectTypeID as _typeId
                FROM feedback WHERE id = :id
                JOIN aspects ON aspects.id = feedback.AspectID
            ");
            $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([])->hydrate($row, Entity::HYDRATE_UNDERSCORE);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /* Instance Methods */

    /**
     * Gets the response rating value.
     *
     * @return double
     */
    public function getValue()
    {
        return (double) $this->get('value');
    }

    /**
     * Gets the response submission date.
     *
     * @return integer
     */
    public function getDate()
    {
        return (int) $this->get('date');
    }

    /**
     * Gets the Aspect Type Id.
     *
     * @return integer
     */
    public function getAspectTypeId()
    {
        return (int) $this->get('typeId');
    }
}
