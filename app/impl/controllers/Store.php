<?php
/**
 * Store | Controller
 *
 * @version v0.0.1 (Mar. 17, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;

use Brv\impl\middleware\Authentication as MiddleAuth;
use Brv\impl\entities\Store as EStore;
use Brv\impl\entities\Company as ECompany;

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
            $company = ECompany::queryId($store->getCompanyId());

            if ($company === null) {
                self::fail("Unexpected error.", \HTTP::SERVER);
            }

            if (!$store->isActive()) {
                self::fail("Specified store is not active.", \HTTP::BAD_PARAMS);
            }

            return new View($this->extract($store, $company));
        }

        self::fail("Invalid store and/or lack of permissions.", \HTTP::BAD_PARAMS);
    }

    /**
     * Gets a list of stores in a company which the active account has access to.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getAll($params = [])
    {
        /* Authentication is enforced as a precondition from the middleware. */
        $account = MiddleAuth::get();

        $company = $account->getCompany();

        $stores = EStore::queryCompany($company->getId());
        if ($stores === null) {
            self::fail("An unexpected error has occured retrieving your account information.", \HTTP::SERVER);
        }

        /* Require READ permissions on the active stores. */
        $stores = array_values(array_filter($stores, function ($store) use ($account) {
            return $store->isActive() && $account->getPermissions($store)->canRead();
        }));

        return new View([
            'company_active' => $company->isExpired() ?
                                $company->getExpiryDate() :
                                $company->isActive(),
            'stores' => array_map(function ($store) use ($company) {
                return $this->extract($store, $company);
            }, $stores)
        ]);
    }

    /**
     * Extracts store information pertinent to the API.
     *
     * @param  EStore   $store
     * @param  ECompany $company
     * @return array
     */
    private function extract(EStore $store, ECompany $company)
    {
        return [
            'id' => $store->getId(),
            'name' => $store->getName(),
            'store_active' => $store->isActive(),
            'company_active' => $company->isExpired() ?
                                $company->getExpiryDate() :
                                $company->isActive(),
            'url' => $store->getURL(),
            'website' => $store->getWebsite()
        ];
    }
}
