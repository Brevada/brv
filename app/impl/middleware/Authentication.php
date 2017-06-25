<?php
/**
 * Authentication | Middleware
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\impl\middleware;

use Brv\core\routing\Middleware;
use Brv\impl\Entities\Account;

/**
 * Authentication middleware which enforces that the user be authenticated,
 * redirecting the user if necessary.
 */
class Authentication extends Middleware
{
    /**
     * Sets the currently logged in user.
     *
     * @param Account $account The account to set as the current authenticated user.
     */
    public static function set(Account $account = null)
    {
        $id = $account !== null ? $account->getId() : null;

        \App::setState(\STATES::AUTH_USER_ID, $id, true);
        \App::setState(\STATES::AUTH_USER, $account);
    }

    /**
     * Gets the currently authenticated user.
     *
     * @return Account
     */
    public static function get()
    {
        /*
         * Check if user is in temporary/transient storage (i.e. in scope of
         * active request). If it's not there, check if Account ID is in storage,
         * session storage can also be checked. If ID found, lookup and cache
         * account. Returns found account. Otherwise, return null.
         */
        $account = \App::getState(\STATES::AUTH_USER);
        if ($account === null) {
            $id = \App::getState(\STATES::AUTH_USER_ID);
            if ($id === null) return null;

            $account = Account::queryId($id);
            if ($account === null) {
                \App::setState(\STATES::AUTH_USER_ID, $id, true);
            }

            \App::setState(\STATES::AUTH_USER, $account);
        }

        return $account;
    }

    /**
     * Gets the next view to process, or redirects if the user is not
     * authenticated.
     *
     * @return \Brv\core\views\View|void
     */
    public function getView()
    {
        if (self::get() === null) {
            // Have user login.
            \App::redirect('login?to=' . urlencode($_SERVER['REQUEST_URI']));
        } else {
            // Redirect admins.
            if (\App::getState(\STATES::AUTH_USER)->getLegacyPermissions() == 255) {
                return \App::redirect('admin');
            }

            // User is logged in.
            return $this->getNext();
        }
    }
}
