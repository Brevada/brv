<?php
/**
 * Middleware Interface
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

/**
 * Middleware acts as a MITM between the beginning of a user's HTTP request
 * and the endpoint - the final content being served.
 */
interface IMiddleware
{
    /**
     * Retrieves the result of the middleware logic.
     *
     * @return Brv\core\views\View|boolean The view on success or false if the
     * route should be skipped.
     */
    public function getView();
}
