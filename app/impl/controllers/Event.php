<?php
/**
 * Event | Controller
 *
 * @version v0.0.1 (Jan. 14, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;

use Brv\impl\middleware\Authentication as MiddleAuth;
use Brv\impl\entities\Event as EEvent;
use Brv\impl\entities\Store as EStore;
use Brv\impl\entities\Aspect as EAspect;
use Brv\core\data\Data;

use Respect\Validation\Validator as v;

/**
 * The Event API.
 */
class Event extends Controller
{

    /**
     * Deletes an individual event by event id.
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

        $event = null;

        /* Load an Event Model. */
        $eventId = self::from(1, $params);
        if ($eventId != null) {
            v::intVal()->min(0)->check($eventId);
            $event = EEvent::queryId(intval($eventId));
        }

        /* User requires WRITE permission for the event. */
        if ($event !== null && $account->getPermissions($event)->canWrite()) {
            if ($event->delete() !== false) {
                return new View([]);
            } else {
                self::fail("Unable to delete event.", \HTTP::SERVER);
            }
        }

        self::fail("Invalid event and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Creates a new event tied to the logged in account.
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

        $title = self::from('title', $this->getBody(), null);
        if ($title === null) {
            self::fail("An event title is required.");
        }

        $title = trim($title);
        /* 100 char limit is schema restriction. */
        v::stringType()->notEmpty()->length(1, 100)->alnum('-"\'')->check($title);

        $maxTime = time() + (\Brv\core\data\Data::SECONDS_YEAR * 10);

        $from = self::from('from_unix', $this->getBody(), null);
        if ($from === null) {
            self::fail("A from date must be specified.");
        }
        v::intVal()->min(0)->max($maxTime)->check($from);

        $to = self::from('to_unix', $this->getBody(), null);
        if ($to !== null) {
            v::intVal()->min($from)->max($maxTime)->check($to);
        }

        // Create and return id.
        $event = new EEvent();
        $event->setTitle($title);
        $event->setStoreId($storeId);
        $event->setFrom($from);
        if ($to !== null) {
            $event->setTo($to);
        }
        try {
            $eventId = $event->commit();
        } catch (\Exception $ex) {
            self::fail("Failed to create new event.");
        }

        return new View([
            'id' => $eventId
        ]);
    }

    /**
     * Gets an individual event by event id.
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

        $event = null;

        /* Load an Event Model. */
        $eventId = self::from(1, $params);
        if ($eventId !== null) {
            v::intVal()->min(0)->check($eventId);
            $event = EEvent::queryId(intval($eventId));
        }

        /* User requires READ permission for the event. */
        if ($event !== null && $account->getPermissions($event)->canRead()) {
            return new View($this->extract($event));
        }

        self::fail("Invalid event and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Gets all events by store id.
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

        /* Load all events by store id. */
        $events = EEvent::queryStore($store->getId());
        if ($events !== null) {
            return new View([
                /* For each event, extract info pertinent to the API. */
                'events' => array_map(function ($event) {
                    return $this->extract($event);
                }, $events)
            ]);
        }

        self::fail("Invalid store and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Extracts data from the event entity which is pertinent to the API.
     *
     * @param EEvent $event The event entity to extract data from.
     *
     * @return array The extracted data.
     */
    private function extract($event)
    {
        $completed = $event->getTo();
        $completed = is_null($completed) ? false : $completed;

        $aspects = $event->getAspects();
        $aspectsData = [];

        $eventData = [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'from' => $event->getFrom(),
            'completed' => $completed,
            'aspects' => [],
            'summary' => [
                'responses' => 0,
                'average' => null,
                'to_all_time' => null
            ]
        ];

        $responses = 0;
        $average_sum = null;
        $to_all_time_sum = null;

        foreach ($aspects as $aspect) {
            // Summary of performance up to the start of the event.
            $before = $aspect->getSummary(0, $event->getFrom());

            $before_responses = self::from('responses', $before, 0);
            $before_average = self::from('average', $before, null);

            // Summary of performance up to the end of the event.
            $thru = $aspect->getSummary(
                0,
                $completed !== false ? $completed : time()
            );

            $thru_responses = self::from('responses', $thru, 0);
            $thru_average = self::from('average', $thru, null);

            // A measure of how much the responses given during the event affected
            // the overall average for the aspect.
            $change = null;
            if ($before_responses > 0 && $thru_responses > 0) {
                $change = $thru_average - $before_average;
            }

            // The number of responses in the time span of the event.
            $delta_responses = $thru_responses - $before_responses;

            $eventData['aspects'][] = [
                'id' => $aspect->getId(),
                'title' => $aspect->getTitle(),
                'change' => $change,
                'responses' => $delta_responses
            ];

            $responses += $delta_responses;
            $average_sum += $thru_average * $delta_responses;
            // TODO Does this make sense?
            $to_all_time_sum += $change * $delta_responses;
        }

        if ($responses > 0 && count($aspects) > 0) {
            $eventData['summary']['responses'] = $responses;
            $eventData['summary']['average'] = $average_sum / (count($aspects)*$responses);
            $eventData['summary']['to_all_time'] = $to_all_time_sum / (count($aspects)*$responses);
        }

        return $eventData;
    }

    /**
     * Unlinks an aspect from an event.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function aspectDelete(array $params = [])
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        $event = null;
        $aspect = null;

        /* Load an Event Model. */
        $eventId = self::from(1, $params);
        if ($eventId != null) {
            v::intVal()->min(0)->check($eventId);
            $event = EEvent::queryId(intval($eventId));
        }

        /* Load an Event Model. */
        $aspectId = self::from(2, $params);
        if ($aspectId != null) {
            v::intVal()->min(0)->check($aspectId);
            $aspect = EAspect::queryId(intval($aspectId));
        }

        /* User requires WRITE permission for the event and for the aspect. */
        if ($event !== null && $aspect !== null && $account->getPermissions($event)->canWrite() && $account->getPermissions($aspect)->canWrite()) {
            if ($event->deleteAspect($aspect->getId()) !== false) {
                return new View([]);
            } else {
                self::fail("Unable to delete aspect from event.", \HTTP::SERVER);
            }
        }

        self::fail("Invalid event or aspect and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Links an aspect to an event.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function aspectLink(array $params = [])
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        $store = null;
        $event = null;
        $aspect = null;

        /* Load a Store Model. */
        $storeId = self::from('store', $this->getBody());
        if ($storeId != null) {
            v::intVal()->min(0)->check($storeId);
            $store = EStore::queryId(intval($storeId));
        }

        if ($store === null || !$account->getPermissions($store)->canWrite()) {
            self::fail("Invalid store and/or lack of permissions.", \HTTP::BAD_PARAMS);
        }

        /* Load an Event Model. */
        $eventId = self::from(1, $params);
        if ($eventId != null) {
            v::intVal()->min(0)->check($eventId);
            $event = EEvent::queryId(intval($eventId));
        }

        if ($event === null || !$account->getPermissions($event)->canWrite()) {
            self::fail("Invalid event and/or lack of permissions.", \HTTP::BAD_PARAMS);
        }

        /* Load aspect by aspect type. */
        $aspectTypeId = self::from('aspect_id', $this->getBody());
        if ($aspectTypeId != null) {
            v::intVal()->min(0)->check($aspectTypeId);
            $aspect = EAspect::queryTypeId(intval($aspectTypeId), $store->getId());
        }

        if ($aspect === null || !$account->getPermissions($aspect)->canWrite()) {
            self::fail("Invalid aspect and/or lack of permissions.", \HTTP::BAD_PARAMS);
        }

        /* User requires WRITE permission for the event and for the aspect. */
        if ($event->linkAspect($aspect->getId()) !== false) {
            return new View([]);
        } else {
            self::fail("Unable to add aspect to event.", \HTTP::SERVER);
        }

        self::fail("Invalid request and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }
}
