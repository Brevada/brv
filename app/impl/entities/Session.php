<?php
/**
 * Session | Entity
 *
 * @version v0.0.1 (Apr. 12, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;

/**
 * An entity representing a feedback session.
 */
class Session extends Entity
{
    use common\Session;

    private $fields = [];

    /**
     * Instantiates a feedback session entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->fields = [];
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate a session entity from an id.
     *
     * @param integer $id
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT id, SessionCode, SubmissionTime, Acknowledged
                FROM session_data WHERE id = :id
            ");
            $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([])->hydrate($row);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Factory method to instantiate a session entity from a session code.
     *
     * @param string $code
     * @return self
     */
    public static function queryCode($code)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT id, SessionCode, SubmissionTime, Acknowledged
                FROM session_data WHERE SessionCode = :code
            ");
            $stmt->bindValue(':code', $code, \PDO::PARAM_STR);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([])->hydrate($row);
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
        try {
            DB::get()->beginTransaction();

            /* create session if doesn't exist */
            $stmt = DB::get()->prepare("
                INSERT IGNORE INTO session_data
                (SessionCode, SubmissionTime, Acknowledged)
                VALUES (:session, :date, 0)
            ");
            $stmt->bindValue(':session', $this->getSessionCode(), \PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->getSubmissionTime(), \PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() === 1) {
                $this->set('id', (int) DB::get()->lastInsertId());
            }

            /* At this point there should be an id. */
            if ($this->get('id') === null) {
                DB::get()->rollBack();
                \App::log()->error("Session id is NULL in commit.");
                return null;
            }

            $stmt = DB::get()->prepare("
                INSERT INTO session_data_field
                (SessionDataID, DataLabel, DataKey, DataValueLarge, DataValueSmall)
                VALUES (:id, :label, :key, :large, :small)
                ON DUPLICATE KEY UPDATE
                DataLabel = :label, DataValueLarge = :large, DataValueSmall = :small
            ");

            foreach ($this->fields as $key => $data) {
                $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
                $stmt->bindValue(':label', $data['label'], \PDO::PARAM_STR);
                $stmt->bindValue(':key', $key, \PDO::PARAM_STR);
                if (strlen($data['value']) < 255) {
                    $stmt->bindValue(':small', $data['value'], \PDO::PARAM_STR);
                    $stmt->bindValue(':large', null, \PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(':large', $data['value'], \PDO::PARAM_STR);
                    $stmt->bindValue(':small', null, \PDO::PARAM_STR);
                }
                $stmt->execute();
            }

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

        return null;
    }

    /**
     * Sets a session data field for the instantiated session.
     *
     * If label is not supplied, it will default to $key, replacing "_" with
     * a space and uppercasing every word.
     *
     * @param string $key
     * @param string $value
     * @param string $label
     */
    public function setField($key, $value, $label = null)
    {
        if ($label === null) $label = ucwords(str_replace('_', ' ', $key));
        $this->fields[$key] = [
            "label" => $label,
            "value" => $value
        ];
    }

    /**
     * Gets a session data field value by key.
     *
     * @param string $key
     * @return string
     */
    public function getField($key)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT IFNULL(DataValueSmall, DataValueLarge) as `value` FROM
                session_data_field WHERE SessionDataID = :id AND DataKey = :key
                LIMIT 1
            ");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->bindValue(':key', $key, \PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return $row['value'];
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
            return null;
        }
    }

    /**
     * Gets the submission date.
     *
     * @return integer
     */
    public function getSubmissionTime()
    {
        return (int) $this->get('SubmissionTime');
    }

    /**
     * Sets the submission date in seconds since epoch (unix time).
     *
     * @param integer $v
     * @return integer
     */
    public function setSubmissionTime($v)
    {
        $this->set('SubmissionTime', (int) $v);
        return $this->getSubmissionTime();
    }

    /**
     * Tests whether session has been acknowledged by the store user.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return ((int) $this->get('Acknowledged')) === 1;
    }

    /**
     * Sets the acknowledged state of the session.
     *
     * @param boolean $v
     * @return boolean
     */
    public function setAcknowledged($v = true)
    {
        $this->set('Acknowledged', (int) $v);
        return $this->isAcknowledged();
    }
}
