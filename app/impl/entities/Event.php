<?php
/**
 * Event | Entity
 *
 * @version v0.0.1 (Jan. 07, 2017)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;

use Brv\impl\entities\Aspect;

/**
 * An entity representing an individual event.
 *
 * An Event considers a collection of aspects in a snapshot of time.
 */
class Event extends Entity
{
    /**
     * Instantiates an event entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate an event entity from an event id.
     *
     * @param integer $id The event id.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    milestones.id as _id, milestones.StoreID as _StoreID,
                    milestones.Title as _Title, milestones.Completed as _Completed,
                    UNIX_TIMESTAMP(milestones.FromDate) as _From,
                    UNIX_TIMESTAMP(milestones.ToDate) as _To
                FROM milestones
                WHERE milestones.id = :id
            ");
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([])->hydrate($row, Entity::HYDRATE_UNDERSCORE);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Factory method to instantiate an array of event entities from a store id.
     *
     * @param integer $id The store id.
     * @param integer $from From unix time to filter events.
     * @param integer $to To unix time to filter events.
     * @return self
     */
    public static function queryStore($id, $from = null, $to = null)
    {
        $from = is_null($from) ? 0 : $from;
        $to = is_null($to) ? time() : $to;

        // We find events which "overlap" from, to.
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    milestones.id as _id, milestones.StoreID as _StoreID,
                    milestones.Title as _Title, milestones.Completed as _Completed,
                    UNIX_TIMESTAMP(milestones.FromDate) as _From,
                    UNIX_TIMESTAMP(milestones.ToDate) as _To
                FROM milestones
                WHERE
                    milestones.StoreID = :id AND
                    (milestones.FromDate >= FROM_UNIXTIME(:from) OR
                    milestones.ToDate > FROM_UNIXTIME(:from)) AND
                    milestones.FromDate < FROM_UNIXTIME(:to)
            ");
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->bindValue(':from', $from, \PDO::PARAM_INT);
            $stmt->bindValue(':to', $to, \PDO::PARAM_INT);
            $stmt->execute();
            return array_map(function ($row) {
                return self::from([])->hydrate($row, Entity::HYDRATE_UNDERSCORE);
            }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /* Instance Methods */

    /**
     * Commits Event to database.
     *
     * @return integer The id of the newly created/updated Event.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') !== -1) {
            /*
             * UPDATE rather than INSERT.
             */
            try {
                $stmt = DB::get()->prepare("
                    UPDATE milestones SET
                    StoreID = :storeId, Title = :title, Completed = :completed,
                    FromDate = FROM_UNIXTIME(:from),
                    ToDate = IF(ISNULL(:to), NULL, FROM_UNIXTIME(:to))
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
                $stmt->bindValue(':storeId', $this->getStoreId(), \PDO::PARAM_INT);
                $stmt->bindValue(':title', $this->getTitle(), \PDO::PARAM_STR);
                $stmt->bindValue(':completed', (int) $this->isCompleted(), \PDO::PARAM_INT);
                $stmt->bindValue(':from', $this->getFrom(), \PDO::PARAM_INT);
                $stmt->bindValue(':to', $this->getTo(), \PDO::PARAM_INT);
                $stmt->execute();
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }

            return null;
        } else {
            /* Completely new event. INSERT. */
            try {
                $stmt = DB::get()->prepare("
                    INSERT INTO milestones SET
                    StoreID = :storeId, Title = :title, Completed = :completed,
                    FromDate = FROM_UNIXTIME(:from),
                    ToDate = IF(ISNULL(:to), NULL, FROM_UNIXTIME(:to))
                ");
                $stmt->bindValue(':storeId', $this->getStoreId(), \PDO::PARAM_INT);
                $stmt->bindValue(':title', $this->getTitle(), \PDO::PARAM_STR);
                $stmt->bindValue(':completed', (int) $this->isCompleted(), \PDO::PARAM_INT);
                $stmt->bindValue(':from', $this->getFrom(), \PDO::PARAM_INT);
                $stmt->bindValue(':to', $this->getTo(), \PDO::PARAM_INT);
                $stmt->execute();
                $this->set('id', (int) DB::get()->lastInsertId());
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }
        }

        return null;
    }

    /**
     * Gets the event id.
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->get('id');
    }

    /**
     * Gets the event store id.
     *
     * @return integer
     */
    public function getStoreId()
    {
        return (int) $this->get('StoreID');
    }

    /**
     * Sets the event store id.
     *
     * @param integer $id The new store id.
     * @return integer
     */
    public function setStoreId($id)
    {
        $this->set('StoreID', (int) $id);
        return $this->getStoreId();
    }

    /**
     * Gets the event from date in seconds from epoch.
     *
     * @return integer
     */
    public function getFrom()
    {
        return (int) $this->get('From');
    }

    /**
     * Sets the event from date.
     *
     * @param integer $seconds The new from date.
     * @return integer
     */
    public function setFrom($seconds)
    {
        $this->set('From', (int) $seconds);
        return $this->getFrom();
    }

    /**
     * Gets the event to date in seconds from epoch.
     *
     * @return integer
     */
    public function getTo()
    {
        $ret = $this->get('To', null);
        if (is_null($ret)) return $ret;
        return (int) $ret;
    }

    /**
     * Sets the event to date.
     *
     * @param integer $seconds The new to date.
     * @return integer
     */
    public function setTo($seconds)
    {
        $this->set('To', is_null($seconds) ? null : (int) $seconds);
        return $this->getTo();
    }

    /**
     * Gets the event title.
     *
     * @return string
     */
    public function getTitle() {
        return $this->get('Title');
    }

    /**
     * Sets the event title.
     *
     * @param $title The new event title.
     * @return string
     */
    public function setTitle($title) {
        $this->set('Title', $title);
        return $this->getTitle();
    }

    /**
     * Checks if the event is "completed".
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return ((int) $this->get('Completed')) === 1;
    }

    /**
     * Sets the events completed state.
     *
     * @param boolean $state The new "completed" state. If null, will assume
     * state based on from and to dates.
     *
     * @return boolean
     */
    public function setCompleted($state = null)
    {
        if ($state === null) {
            if ($this->getFrom() === null || $this->getTo() === null) {
                $state = false;
            } else {
                $state = $this->getTo() <= time();
            }
        }
        $this->set('Completed', (int) $state);
        return $this->isCompleted();
    }

    /**
     * Gets an array of aspect entities belonging to the current Event.
     *
     * @return Aspect[]
     */
    public function getAspects()
    {
        $aspects = [];

        try {
            $stmt = DB::get()->prepare("
                SELECT
                    milestone_aspects.AspectID as id
                FROM milestone_aspects
                WHERE milestone_aspects.MilestoneID = :id
            ");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->execute();
            $aspectIds = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
            return Aspect::queryIds($aspectIds);
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return $aspects;
    }

    /**
     * Deletes the event.
     *
     * @return boolean False on failure.
     */
    public function delete()
    {
        /*
         * TODO
         * Much of this logic can be replaced with appropriate FOREIGN KEYS
         * in the schema, using ON DELETE restrictions.
         */

        try {
            DB::get()->beginTransaction();

            // Delete event <-> aspect links.
            $stmt = DB::get()->prepare("
                DELETE FROM milestone_aspects
                WHERE milestone_aspects.MilestoneID = :id
            ");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->execute();

            // Delete event.
            $stmt = DB::get()->prepare("
                DELETE FROM milestones
                WHERE milestones.id = :id
            ");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->execute();

            DB::get()->commit();
            return true;
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        try {
            DB::get()->rollBack();
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return false;
    }

    /**
     * Unlinks an aspect from the event.
     *
     * @param integer $id The aspect id to unlink.
     * @return boolean False on failure.
     */
    public function deleteAspect($id)
    {
        try {
            // Delete aspect link.
            $stmt = DB::get()->prepare("
                DELETE FROM milestone_aspects
                WHERE milestone_aspects.MilestoneID = :event_id
                AND milestone_aspects.AspectID = :aspect_id
            ");
            $stmt->bindValue(':event_id', $this->getId(), \PDO::PARAM_INT);
            $stmt->bindValue(':aspect_id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return false;
    }

    /**
     * Links an aspect to the event.
     *
     * @param integer $id The aspect id to link.
     * @return boolean False on failure.
     */
    public function addAspect($id)
    {
        try {
            // Create aspect link.
            $stmt = DB::get()->prepare("
                INSERT INTO milestone_aspects
                SET MilestoneID = :event_id,
                AspectID = :aspect_id
            ");
            $stmt->bindValue(':event_id', $this->getId(), \PDO::PARAM_INT);
            $stmt->bindValue(':aspect_id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return false;
    }

    /**
     * Checks if an event has an aspect linked to it.
     *
     * @param integer $id The aspect id to check.
     * @return boolean False on failure.
     */
    public function hasAspect($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT 1 FROM milestone_aspects
                WHERE MilestoneID = :event_id AND
                AspectID = :aspect_id
            ");
            $stmt->bindValue(':event_id', $this->getId(), \PDO::PARAM_INT);
            $stmt->bindValue(':aspect_id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return false;
    }
}
