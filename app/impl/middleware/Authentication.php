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
        \App::setState(\STATES::AUTH_USER, $account, true);
    }

    /**
     * Gets the currently authenticated user.
     *
     * @return Account
     */
    public static function get()
    {
        return \App::getState(\STATES::AUTH_USER);
    }

    /**
     * Gets the next view to process, or redirects if the user is not
     * authenticated.
     *
     * @return \Brv\core\views\View|void
     */
    public function getView()
    {
        if (\App::getState(\STATES::AUTH_USER) === null) {
            // Have user login.
            \App::redirect('login/to' . $_SERVER['REQUEST_URI']);
        } else {
            // User is logged in.
            return $this->getNext();
        }
    }
}
