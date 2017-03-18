<?php
/**
 * AspectType | Controller
 *
 * @version v0.0.1 (Dec. 31, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;

use Brv\impl\middleware\Authentication as MiddleAuth;
use Brv\impl\entities\Store as EStore;
use Brv\impl\entities\Industry as EIndustry;
use Brv\impl\entities\Event as EEvent;

use Respect\Validation\Validator as v;

/**
 * The AspectType API.
 */
class AspectType extends Controller
{
    /**
     * Gets all aspect types available to an industry.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getIndustry(array $params = [])
    {
        $account = MiddleAuth::get();

        // If set, aspect types in use by store will be excluded.
        $excludeStore = self::from('exclude_store', $_GET, null);
        if ($excludeStore !== null) {
            v::intVal()->min(0)->check($excludeStore);

            /* Check permissions. */
            $store = EStore::queryId(intval($excludeStore));
            if ($store === null || !$account->getPermissions($store)->canRead()) {
                self::fail("Excluded store is invalid or missing necessary permissions.", \HTTP::BAD_PARAMS);
            }
        }

        /* Load all aspect types by industry. */
        $industry = EIndustry::queryCompany($account->getCompany()->getId());
        $aspectTypes = $industry->getAspectTypes($excludeStore);
        return new View([
            "aspect_types" => array_values(array_map(function ($type) {
                return [
                    'id' => $type->getId(),
                    'title' => $type->getTitle(),
                    'custom' => $type->getCustom()
                ];
            }, $aspectTypes))
        ]);
    }

    /**
     * Gets all aspect types available to an event, excluding the types already
     * used by the event.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getEvent(array $params = [])
    {
        $account = MiddleAuth::get();

        $event = null;

        /* Load an Event Model. */
        $eventId = self::from(1, $params);
        if ($eventId != null) {
            v::intVal()->min(0)->check($eventId);
            $event = EEvent::queryId(intval($eventId));
        }

        if ($event === null || !$account->getPermissions($event)->canRead()) {
            self::fail("Event is invalid or missing necessary permissions.", \HTTP::BAD_PARAMS);
        }

        /* Load all aspect types by industry. */
        $industry = EIndustry::queryCompany($account->getCompany()->getId());
        $allAspectTypes = $industry->getAspectTypes();

        $eventAspectTypes = array_map(function ($aspect) {
            return $aspect->getAspectTypeId();
        }, $event->getAspects());

        $filtered = array_filter($allAspectTypes, function ($type) use ($eventAspectTypes) {
            return array_search($type->getId(), $eventAspectTypes) === false;
        });

        return new View([
            "aspect_types" => array_values(array_map(function ($type) {
                return [
                    'id' => $type->getId(),
                    'title' => $type->getTitle(),
                    'custom' => $type->getCustom()
                ];
            }, $filtered))
        ]);
    }
}
