<?php
/**
 * Aspect | Controller
 *
 * @version v0.0.1 (Dec. 31, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;

use Brv\impl\middleware\Authentication as MiddleAuth;
use Brv\impl\entities\Store as EStore;
use Brv\impl\entities\Aspect as EAspect;
use Brv\impl\entities\AspectType as EAspectType;
use Brv\impl\entities\Industry as EIndustry;

use Respect\Validation\Validator as v;

/**
 * The Aspect API.
 */
class Aspect extends Controller
{

    /**
     * Deletes an individual aspect by aspect id.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function delete(array $params = [])
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        $aspect = null;

        /* Load an Aspect Model. */
        $aspectId = self::from(1, $params);
        if ($aspectId != null) {
            v::intVal()->min(0)->check($aspectId);
            $aspect = EAspect::queryId(intval($aspectId));
        }

        /* User requires WRITE permission for the aspect. */
        if ($aspect != null && $account->getPermissions($aspect)->canWrite()) {
            if ($aspect->delete($account->getCompany()->getId()) !== false) {
                return new View([]);
            } else {
                self::fail("Unable to delete aspect.", \HTTP::SERVER);
            }
        }

        self::fail("Invalid aspect and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Creates a new aspect tied to the logged in account.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function create(array $params = [])
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        $storeId = self::from('store', $this->getBody(), null);
        if ($storeId === null) {
            self::fail("A store must be specified.");
        }
        v::intVal()->min(0)->check($storeId);

        /* Check WRITE permissions for store. */
        $store = EStore::queryId($storeId);
        if ($store == null || !$account->getPermissions($store)->canWrite()) {
            self::fail("Store is invalid or missing necessary permissions.", \HTTP::BAD_PARAMS);
        }

        $id = self::from('aspect_id', $this->getBody(), -1);
        if ($id !== null) {
            v::intVal()->min(-1)->check($id);
            $id = intval($id);
        }

        $title = self::from('aspect', $this->getBody(), null);
        if ($title !== null) {
            $title = trim($title);
            /* 50 char limit is schema restriction. */
            v::stringType()->notEmpty()->length(2, 50)->alnum('-"\'')->check($title);
        }

        if ($title === null && $id === -1) {
            self::fail("No aspect specified.");
        }

        // If id=-1, check if title already exists (that's accessible).
        $types = $account->getCompany()->getIndustry()->getAspectTypes();

        if ($id === -1) {
            // Verify title is not in $types.
            $type = current(array_filter($types, function ($type) use ($title) {
                return strcasecmp($type->getTitle(), $title) === 0;
            }));
            if ($type !== false) {
                // Title already exists.
                $id = $type->getId();
            }
        } elseif ($id !== -1 && current(array_filter($types, function ($type) use ($id) {
            return $type->getId() == $id;
        })) === false) {
            // Verify that it is valid id in $types.
            // ID is invalid.
            self::fail("The selected aspect does not exist.");
        }

        if ($id === -1) {
            /* Create aspect.
             * TODO: Since custom types are per company, check company write permissions?
             */
            $aspectType = new EAspectType([
                'Title' => $title,
                'CompanyID' => $account->getCompany()->getId()
            ]);
            try {
                $id = $aspectType->commit();
            } catch (\Exception $ex) {
                self::fail("Failed to create new aspect type.");
            }
        }

        if ($id === -1 || $id === null) {
            self::fail("Failed to create new aspect type due to internal error.");
        }

        // Create and return id.
        $aspect = new EAspect();
        $aspect->setStoreId($storeId);
        $aspect->setAspectTypeId($id);
        $aspect->setActive(true);
        try {
            $aspectId = $aspect->commit();
        } catch (\Exception $ex) {
            self::fail("Failed to create new aspect.");
        }

        return new View([
            'id' => $aspectId
        ]);
    }

    /**
     * Gets an individual aspect by aspect id.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function get(array $params = [])
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        $aspect = null;

        /* Load an Aspect Model. */
        $aspectId = self::from(1, $params);
        if ($aspectId != null) {
            v::intVal()->min(0)->check($aspectId);
            $aspect = EAspect::queryId(intval($aspectId));
        }

        /* Parse the data span or default to false (which indicates no data). */
        $dataSpan = self::from('days', $_GET, false);
        if ($dataSpan !== false) {
            /* Arbitrarily max the span at 100 years. Acts as a barier against typos. */
            v::intVal()->min(0)->max(36500)->check($dataSpan);
            $dataSpan = intval($dataSpan);
        }

        /* Parse the number of points. */
        $numPoints = self::from('points', $_GET, false);
        if ($numPoints !== false) {
            v::intVal()->min(1)->max(180)->check($numPoints);
            $numPoints = intval($numPoints);
        }

        if ($aspect != null && $aspect->isActive()) {
            $store = EStore::queryId($aspect->getStoreId());
            /* User requires READ permission for the store. */
            if ($store != null && $account->getPermissions($store)->canRead()) {
                return new View($this->extract($aspect, $dataSpan, $numPoints));
            }
        }

        self::fail("Invalid aspect and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Gets all aspects by store id.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getAll(array $params = [])
    {
        $account = MiddleAuth::get();

        $storeId = self::from('store', $_GET);
        v::intVal()->min(0)->check($storeId);

        /* Load store and check permissions. */
        $store = EStore::queryId(intval($storeId));
        if ($store == null || !$account->getPermissions($store)->canRead()) {
            self::fail("Invalid store and/or lack of permissions.", \HTTP::BAD_PARAMS);
        }

        /* Parse the data span or default to false (no data). */
        $dataSpan = self::from('days', $_GET, false);
        if ($dataSpan !== false) {
            v::intVal()->min(0)->max(36500)->check($dataSpan);
            $dataSpan = intval($dataSpan);
        }

        /* Parse the number of points. */
        $numPoints = self::from('points', $_GET, false);
        if ($numPoints !== false) {
            v::intVal()->min(1)->max(180)->check($numPoints);
            $numPoints = intval($numPoints);
        }

        /* Load all aspects by store id. */
        $aspects = EAspect::queryStore($store->getId());
        if ($aspects !== null) {
            $aspects = array_values(array_filter($aspects, function ($aspect) {
                return $aspect->isActive();
            }));

            return new View([
                /* For each aspect, extract info pertinent to the API. */
                'aspects' => array_map(function ($aspect) use ($dataSpan, $numPoints) {
                    return $this->extract($aspect, $dataSpan, $numPoints);
                }, $aspects)
            ]);
        }

        self::fail("Invalid store and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Extracts data from the aspect entity which is pertinent to the API.
     *
     * @param EAspect $aspect The aspect entity to extract data from.
     * @param integer|boolean $dataSpan Days to retrieve data for, or false for no data.
     * @param integer|boolean $numPoints The number of groups to divide the details into.
     * @return array The extracted data.
     */
    private function extract($aspect, $dataSpan = false, $numPoints = false)
    {
        $aspectData = [
            'id' => $aspect->getId(),
            'title' => $aspect->getTitle(),
            'description' => $aspect->getDescription(),
            'active' => $aspect->isActive() == 1,
            'custom' => $aspect->isCustom() == 1
        ];

        if ($dataSpan !== false) {
            /* Only get details if a data span is supplied. */
            $numPoints = $numPoints === false ? 7 : $numPoints;

            /* For industry comparisons. */
            $account = MiddleAuth::get();
            $industry = $account->getCompany()->getIndustry();

            $aspectData['summary'] = $aspect->getDetails($industry, $dataSpan, $numPoints);
        }

        return $aspectData;
    }
}
