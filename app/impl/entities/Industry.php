<?php
/**
 * Industry | Entity
 *
 * @version v0.0.1 (Dec. 30, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;
use Brv\core\data\IResponse;
use Brv\core\data\SimpleResponse;
use Brv\impl\entities\AspectType;

/**
 * An entity representing an industry.
 */
class Industry extends Entity
{
    /**
     * Instantiates an industry entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate an industry entity from a company id.
     *
     * @param integer $id The company id.
     * @return self
     */
    public static function queryCompany($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    company_categories.id as Category,
                    (
                        SELECT GROUP_CONCAT(
                            DISTINCT company_keywords_link.CompanyKeywordID
                            SEPARATOR ','
                        ) as k
                        FROM company_keywords_link
                        JOIN company_keywords ON company_keywords_link.CompanyKeywordID = company_keywords.id
                        WHERE company_keywords_link.CompanyID = :id
                        GROUP BY company_keywords_link.CompanyID
                    ) as Keywords
                FROM companies
                JOIN company_categories ON company_categories.id = companies.CategoryID
                WHERE companies.id = :id
            ");
            $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([
                    "Category" => $row['Category'],
                    "Keywords" => isset($row['Keywords']) ? explode(',', $row['Keywords']) : null,
                    "CompanyID" => $id
                ]);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Gets the company id for which the industry was calculated for.
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return (int) $this->get('CompanyID');
    }

    /**
     * Gets the category id of the industry.
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return (int) $this->get('Category');
    }

    /**
     * Gets an array of keyword ids for the industry.
     *
     * @return integer[]
     */
    public function getKeywords()
    {
        return $this->get('Keywords');
    }

    /**
     * Gets the average of the aspect type in the industry.
     *
     * @todo Re-examine the WHERE clause to ensure correct responses are included.
     *
     * @param integer $aspectTypeId The id of the aspect type.
     * @return IResponse
     */
    public function getAspectAverage($aspectTypeId)
    {
        $keywords = $this->getKeywords();
        if ($keywords === null) {
            $keywords = [];
        }
        $keywords = '('.implode(',', $keywords).')';

        try {
            $stmt = DB::get()->prepare("
                SELECT AVG(T.value) as avg FROM
                    (SELECT DISTINCT fb.id as id, fb.Rating as value
                    FROM feedback fb
                    JOIN aspects ON aspects.id = fb.AspectID
                    JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID
                    JOIN stores ON stores.id = aspects.StoreID
                    JOIN companies ON companies.id = stores.CompanyID
                    JOIN company_keywords_link ON companies.id = company_keywords_link.CompanyID
                    JOIN company_keywords ON company_keywords.id = company_keywords_link.CompanyKeywordID
                    WHERE
                        aspect_type.id = :aspect_type_id AND
                        aspects.Active = 1 AND
                        stores.Active = 1 AND
                        companies.Active = 1 AND
                        companies.CategoryID = :category_id AND
                        company_keywords.id IN {$keywords}
                    ) as T
            ");
            $stmt->bindValue(':aspect_type_id', $aspectTypeId, \PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $this->getCategoryId(), \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                if ($row['avg'] === null) {
                    return null;
                }
                return new SimpleResponse((double) $row['avg'], 0);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Gets all the aspect types for the industry, optionally excluding those
     * in use by a store.
     *
     * @todo Move to AspectType entity class?
     *
     * @param integer $excludeStore Store to exclude.
     * @return AspectType[]
     */
    public function getAspectTypes($excludeStore = null)
    {
        $types = [];

        // There will be no matches if -1 (alternative is to dynamically build SQL).
        if ($excludeStore === null) {
            $excludeStore = -1;
        }

        try {
            $stmt = DB::get()->prepare("
                SELECT
                    aspect_type.*, NOT(ISNULL(aspect_type.CompanyID)) as custom,
                    frequencies.cnt as frequency
                FROM aspect_type
                JOIN (
                    SELECT COUNT(*) as cnt, aspects.AspectTypeID as id
                    FROM aspects
                    WHERE aspects.Active = 1
                    GROUP BY aspects.AspectTypeID
                ) frequencies ON frequencies.id = aspect_type.id
                WHERE
                    (aspect_type.CompanyID IS NULL OR aspect_type.CompanyID = :company_id) AND
                    NOT EXISTS (
                        SELECT 1 FROM aspects
                        WHERE
                            aspects.AspectTypeID = aspect_type.id AND
                            aspects.StoreID = :store_id AND
                            aspects.Active = 1
                    )
                ORDER BY custom DESC, frequency DESC
            ");

            $stmt->bindValue(':company_id', $this->getCompanyId(), \PDO::PARAM_INT);
            $stmt->bindValue(':store_id', $excludeStore, \PDO::PARAM_INT);
            $stmt->execute();

            $types = array_map(function ($row) {
                return AspectType::from([
                    'id' => $row['id'],
                    'Title' => $row['Title'],
                    'CompanyID' => $row['CompanyID']
                ]);
            }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        } catch (\Exception $ex) {
            \App::log()->error($ex->getMessage());
        }

        return $types;
    }
}
