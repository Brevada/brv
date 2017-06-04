<?php
/**
 * NoAuthentication | Middleware
 *
 * @version v0.0.1 (May 22, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\middleware;

use Brv\core\routing\Middleware;
use Brv\impl\middleware\Authentication;

/**
 * Redirects user to dashboard if authenticated.
 */
class NoAuthentication extends Middleware
{
    /**
     * Gets the next view to process, or redirects if the user is
     * authenticated.
     *
     * @return \Brv\core\views\View|void
     */
    public function getView()
    {
        if (Authentication::get() !== null) {
            // User is logged in.
            \App::redirect('dashboard');
        } else {
            // User is not logged in.
            return $this->getNext();
        }
    }
}
