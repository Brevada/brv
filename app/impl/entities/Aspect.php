<?php
/**
 * Aspect | Entity
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;

use Brv\impl\entities\Response;
use Brv\impl\entities\Industry;
use Brv\core\data\Data;

/**
 * An entity representing an individual aspect, with methods to access
 * aspect data.
 */
class Aspect extends Entity
{
    /**
     * Instantiates an aspect entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate an aspect entity from an aspect id.
     *
     * @param integer $id The aspect id.
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    aspects.*, aspect_type.Title,
                    NOT(ISNULL(aspect_type.CompanyID)) as custom
                FROM aspects
                JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID
                WHERE aspects.id = :id
            ");
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
     * Factory method to instantiate an array of aspect entities from an array
     * of aspect ids.
     *
     * @param integer[] $ids The aspect ids array.
     * @return self[]
     */
    public static function queryIds($ids)
    {
        try {
            $idsList = implode(',', array_map('intval', $ids));
            if (empty($idsList)) return [];

            $stmt = DB::get()->prepare("
                SELECT
                    aspects.*, aspect_type.Title,
                    NOT(ISNULL(aspect_type.CompanyID)) as custom
                FROM aspects
                JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID
                WHERE aspects.id IN ({$idsList})
            ");
            $stmt->execute();
            return array_map(function ($row) {
                return self::from($row);
            }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        } catch (\Exception $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Factory method to instantiate an array of aspect entities from a store id.
     *
     * This method retrieves all aspects belonging to a particular store.
     *
     * @param integer $storeId The id of the store that the aspects belong to.
     * @return self[]
     */
    public static function queryStore($storeId)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    aspects.*, aspect_type.Title,
                    NOT(ISNULL(aspect_type.CompanyID)) as custom
                FROM aspects
                JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID
                WHERE aspects.StoreID = :id
            ");
            $stmt->bindValue(':id', $storeId, \PDO::PARAM_INT);
            $stmt->execute();
            return array_map(function ($row) {
                return self::from($row);
            }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /* Instance Methods */

    /**
     * Commits Aspect to database.
     *
     * @return integer The id of the newly created/updated Aspect.
     */
    public function commit()
    {
        if ($this->has('id') && $this->get('id') !== -1) {
            /*
             * UPDATE rather than INSERT.
             * Only allow Active to be toggled?
             */
            try {
                $stmt = DB::get()->prepare("UPDATE aspects SET Active = :active WHERE id = :id");
                $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
                $stmt->bindValue(':active', (int) $this->isActive(), \PDO::PARAM_INT);
                $stmt->execute();
                return $this->getId();
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
            }

            return null;
        } else {
            /*
             * INSERT rather than UPDATE.
             * On DUPLICATE Store+AspectType, UPDATE.
             * TODO: Move this "DUPLICATE" logic to a UNIQUE KEY in the schema.
             */
            try {
                $stmt = DB::get()->prepare("
                    SELECT id FROM aspects
                    WHERE AspectTypeID = :aspectType AND StoreID = :store
                ");
                $stmt->bindValue(':aspectType', $this->getAspectTypeId(), \PDO::PARAM_INT);
                $stmt->bindValue(':store', $this->getStoreId(), \PDO::PARAM_INT);
                $stmt->execute();

                if (($id = $stmt->fetchColumn()) !== false) {
                    $this->set('id', (int) $id);
                    return $this->commit();
                }
            } catch (\PDOException $ex) {
                \App::log()->error($ex->getMessage());
                return null;
            }

            /* Completely new aspect. INSERT. */
            try {
                $stmt = DB::get()->prepare("INSERT INTO aspects (AspectTypeID, StoreID, Active) VALUES (:aspectType, :store, :active)");
                $stmt->bindValue(':aspectType', $this->getAspectTypeId(), \PDO::PARAM_INT);
                $stmt->bindValue(':store', $this->getStoreId(), \PDO::PARAM_INT);
                $stmt->bindValue(':active', (int) $this->getActive(), \PDO::PARAM_INT);
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
     * Gets a detailed overview of the aspect's data for a particular duration of time.
     *
     * Setting $days = 0 defaults to all time.
     *
     * @api
     *
     * @param Industry $industry The industry entity to compare against.
     * @param integer $days The number of days to include in the data overview.
     * @param integer $numPoints The number of groups to divide the overview into.
     * @return array The data for a particular aspect.
     */
    public function getDetails(Industry $industry = null, $days = 0, $numPoints = 1)
    {
        $summary = [];

        $to = time();
        $from = Data::infDay($to - Data::SECONDS_DAY * $days);
        if ($days == 0) {
            $from = 0;
        }

        $allData = new Data(Response::queryAspect($this->getId(), 0, $to));
        $aspectData = $allData->subsetTime($from, $to);

        $industryData = new Data([]);
        if ($industry !== null) {
            $industryAvg = $industry->getAspectAverage($this->getAspectTypeId());
            if ($industryAvg !== null) {
                $industryData = new Data([$industryAvg], [
                    /*
                        Indicates cached data is acceptable. Currently unimplemented.
                        Essentially, if "cache" === true, then the input responses may not
                        contain all the original data. Instead, it may aggregate responses into
                        a single "average" or single "count" (not currently possible).
                        @TODO Provide support for cache indication.
                    */
                    "cache" => true
                ]);
            }
        }

        $summary['average'] = $aspectData->getAverage();
        $summary['responses'] = $aspectData->getCount();
        $summary['to_all_time'] = $aspectData->getAverageDiff($allData);
        $summary['to_industry'] = $aspectData->getAverageDiff($industryData);

        $summary['data'] = array_map(function ($group) {
            return [
                'from' => $group->getFrom(),
                'to' => $group->getTo(),
                'average' => $group->getAverage(),
                'responses' => $group->getCount()
            ];
        }, $aspectData->group($numPoints, Data::infDay($aspectData->getEarliestDate()), $aspectData->getLatestDate()));

        return $summary;
    }

    /**
     * Gets a summary of the aspect for a particular duration of time.
     *
     * @api
     *
     * @param integer $from The start date of the data to consider.
     * @param integer $days The end date of the data to consider.
     *
     * @return array A summary of this aspect.
     */
    public function getSummary($from, $to)
    {
        // TODO Can be changed to a MySQL query for better performance.

        $summary = [];

        // TODO Should "All Time" consider past the end date?
        $allData = new Data(Response::queryAspect($this->getId(), 0, $to));
        $aspectData = $allData->subsetTime($from, $to);

        $summary['average'] = $aspectData->getAverage();
        $summary['responses'] = $aspectData->getCount();
        $summary['to_all_time'] = $aspectData->getAverageDiff($allData);

        return $summary;
    }

    /**
     * Gets the aspect id.
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->get('id');
    }

    /**
     * Gets the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('Title');
    }

    /**
     * Gets the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->get('Description');
    }

    /**
     * Gets the store id of the aspect.
     *
     * @return integer
     */
    public function getStoreId()
    {
        return (int) $this->get('StoreID');
    }

    /**
     * Sets the store id of the aspect.
     *
     * @param integer The new store id.
     * @return integer
     */
    public function setStoreId($id)
    {
        $this->set('StoreID', $id);
        return $this->getStoreId();
    }

    /**
     * Gets the aspect type id of the aspect.
     *
     * @return integer
     */
    public function getAspectTypeId()
    {
        return (int) $this->get('AspectTypeId');
    }

    /**
     * Sets the aspect type id of the aspect.
     *
     * @param integer The new aspect type id.
     * @return integer
     */
    public function setAspectTypeId($id)
    {
        $this->set('AspectTypeId', (int) $id);
        return $this->getAspectTypeId();
    }

    /**
     * Checks if the aspect is active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return ((int) $this->get('Active')) === 1;
    }

    /**
     * Sets the aspect's active state.
     *
     * @param boolean $state The new "active" state.
     * @return boolean
     */
    public function setActive($state)
    {
        $this->set('Active', (int) $state);
        return $this->getActive();
    }

    /**
     * Checks if the aspect is of custom type.
     *
     * @return boolean
     */
    public function getCustom()
    {
        return ((int) $this->get('custom')) === 1;
    }

    /**
     * Deletes the aspect.
     *
     * The aspect is fully deleted if there is no data linked to it and it is
     * owned by the company who is deleting it.
     *
     * @param integer $companyId The company id deleting the aspect.
     * @return boolean False on failure.
     */
    public function delete($companyId = null)
    {
        /*
         * TODO
         * Much of this logic can be replaced with appropriate FOREIGN KEYS
         * in the schema, using ON DELETE restrictions.
         */

        try {
            DB::get()->beginTransaction();

            // Mark as Inactive.
            $stmt = DB::get()->prepare("UPDATE aspects SET Active = 0 WHERE aspects.id = :id");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->execute();

            // Delete aspect if no data.
            $stmt = DB::get()->prepare("
                DELETE aspects FROM aspects
                WHERE aspects.id = :id AND
                NOT EXISTS (
                    SELECT 1 FROM feedback
                    WHERE feedback.AspectID = aspects.id
                    LIMIT 1
                )
            ");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->execute();

            // Delete aspect type if company owned and there's no aspect referring to it.
            if ($companyId !== null) {
                $stmt = DB::get()->prepare("
                    DELETE aspect_type FROM aspect_type
                    JOIN companies ON companies.id = aspect_type.CompanyID
                    WHERE
                        aspect_type.id = :id AND companies.id = :company_id AND
                        NOT EXISTS (
                            SELECT 1 FROM aspects
                            WHERE aspects.AspectTypeID = aspect_type.id
                            LIMIT 1
                        )
                ");
                $stmt->bindValue(':id', (int) $this->get('AspectTypeID'), \PDO::PARAM_INT);
                $stmt->bindValue(':company_id', (int) $companyId, \PDO::PARAM_INT);
                $stmt->execute();
            }

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
}
