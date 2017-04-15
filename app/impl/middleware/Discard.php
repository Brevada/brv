<?php
/**
 * Discard | Middleware
 *
 * @version v0.0.1 (Apr. 11, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\middleware;

use Brv\core\routing\Middleware;

/**
 * Discards previous middleware's resultant views.
 */
class Discard extends Middleware
{

    /**
     * Gets the next view to process.
     *
     * @return \Brv\core\views\View|void
     */
    public function getView()
    {
        /* "Discards" previous middleware. True => Continue on to controller. */
        return true;
    }
}
