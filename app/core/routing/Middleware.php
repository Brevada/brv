<?php
/**
 * Middleware
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

use Brv\core\routing\IMiddleware;
use Brv\core\views\View;

/**
 * Middleware acts as a MITM between the beginning of a user's HTTP request
 * and the endpoint - the final content being served.
 *
 * Middleware can be chained together to combine both simple and complex logic,
 * ensuring or setting up a particular environment for the endpoint.
 * E.g. requiring the user to be authenticated, or manipulating presentational
 * data before being processed by the "next" view.
 */
abstract class Middleware implements IMiddleware
{
    /** @var Brv\core\views\View The view which the middleware is intercepting. */
    private $next;

    /**
     * Middleware is instantiated with the "next" View.
     *
     * @param Brv\core\views\View $view The view which is to be intercepted.
     */
    public function __construct(View $view)
    {
        $this->next = $view;
    }

    /**
     * Retrieves the result of the middleware logic.
     *
     * @return Brv\core\views\View|boolean The view on success or false if the
     * route should be skipped.
     */
    abstract public function getView();

    /**
     * Gets the "next" view.
     *
     * @return Brv\core\views\View
     */
    protected function getNext()
    {
        return $this->next;
    }
}
