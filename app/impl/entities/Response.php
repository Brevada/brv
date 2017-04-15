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
    use common\Location,
        common\LocationId,
        common\UserAgent,
        common\UserAgentId,
        common\Session,
        common\IPAddress,
        common\AspectId;

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
     * Commits Response to database.
     *
     * @return integer The id of the newly created/updated response.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') !== -1) {
            throw new \Exception("Responses cannot be modified.");
        } else {
            /* New response. INSERT. */
            if ($this->getAspectId() === null) {
                throw new \Exception("Response must be associated with an aspect.");
            }

            try {
                DB::get()->beginTransaction();

                /* create session if doesn't exist */
                $stmt = DB::get()->prepare("
                    INSERT IGNORE INTO session_data
                    (SessionCode, SubmissionTime, Acknowledged)
                    VALUES (:session, :date, 0)
                ");
                $stmt->bindValue(':session', $this->getSessionCode(), \PDO::PARAM_STR);
                $stmt->bindValue(':date', $this->getDate(), \PDO::PARAM_INT);
                $stmt->execute();

                /* Create location entry. Optional. */
                if ($this->getCountry() !== null) {
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
                }

                // create user agent entry.
                $stmt = DB::get()->prepare("
                    INSERT INTO user_agents
                    (UserAgent, TabletID)
                    VALUES (:userAgent, :tabletId)
                ");
                $stmt->bindValue(':userAgent', $this->getUserAgent(), \PDO::PARAM_STR);
                $stmt->bindValue(':tabletId', $this->getTabletId(), \PDO::PARAM_INT);
                $stmt->execute();
                $this->setUserAgentId(DB::get()->lastInsertId());

                $stmt = DB::get()->prepare("
                    INSERT INTO feedback SET
                    AspectID = :aspectId, Date = FROM_UNIXTIME(:date),
                    Rating = :value, IPAddress = :ipAddress,
                    SessionCode = :session, UserAgentID = :userAgentId,
                    LocationID = :locationId
                ");
                $stmt->bindValue(':value', $this->getValue(), \PDO::PARAM_STR);
                $stmt->bindValue(':aspectId', $this->getAspectId(), \PDO::PARAM_INT);
                $stmt->bindValue(':date', $this->getDate(), \PDO::PARAM_INT);
                $stmt->bindValue(':ipAddress', $this->getIPAddress(), \PDO::PARAM_STR);
                $stmt->bindValue(':session', $this->getSessionCode(), \PDO::PARAM_STR);
                $stmt->bindValue(':userAgentId', $this->getUserAgentId(), \PDO::PARAM_INT);
                $stmt->bindValue(':locationId', $this->getLocationId(), \PDO::PARAM_INT);
                $stmt->execute();
                $this->set('id', (int) DB::get()->lastInsertId());

                DB::get()->commit();

                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
                try {
                    DB::get()->rollBack();
                } catch (\PDOException $ex) {
                    \App::log()->error($ex->getMessage());
                }
            }
        }

        return null;
    }

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
     * Sets the response rating value.
     *
     * @param double $v
     * @return double
     */
    public function setValue($v)
    {
        $this->set('value', (double) $v);
        return $this->getValue();
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
     * Sets the response date in seconds since epoch (unix time).
     *
     * @param integer $v
     * @return integer
     */
    public function setDate($v)
    {
        $this->set('date', (int) $v);
        return $this->getDate();
    }

    /**
     * Gets the aspect type id.
     *
     * @return integer
     */
    public function getAspectTypeId()
    {
        return (int) $this->get('typeId');
    }

    /**
     * Sets the aspect type id.
     *
     * @param integer $v
     * @return integer
     */
    public function setAspectTypeId($v)
    {
        $this->set('typeId', (int) $v);
        return $this->getAspectTypeId();
    }
}
