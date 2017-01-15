<?php
/**
 * Store | Controller
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;

use Brv\impl\middleware\Authentication as MiddleAuth;
use Brv\impl\entities\Store as EStore;

use Respect\Validation\Validator as v;

/**
 * The Store API.
 */
class Store extends Controller
{
    /**
     * Gets a single store by store id or if no store id is supplied, it will
     * serve a "default" store.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function get($params = [])
    {
        /* Authentication is enforced as a precondition from the middleware. */
        $account = MiddleAuth::get();

        $store = null;

        /* Load the store by id or by account (in case of default). */
        $storeId = self::from(2, $params);
        if ($storeId != null) {
            v::intVal()->min(0)->check($storeId);
            $store = EStore::queryId(intval($storeId));
        } else {
            $store = EStore::queryDefault($account->getId());
        }

        /* Require READ permissions on the store. */
        if ($store != null && $account->getPermissions($store)->canRead()) {
            return new View([
                'id' => $store->getId(),
                'name' => $store->getName(),
                'active' => $store->isActive(),
                'url' => $store->getURL(),
                'website' => $store->getWebsite()
            ]);
        }

        self::fail("Invalid store and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }
}
