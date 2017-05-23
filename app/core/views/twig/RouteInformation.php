<?php
/**
 * Twig Route Information functions
 *
 * @version v0.0.1 (May 23, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\core\views\twig;

/**
 * Twig Extension for retrieving route information.
 */
class RouteInformation extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            'brv_url' => new \Twig_Function_Method($this, 'getBrvUrl'),
            'canonical' => new \Twig_Function_Method($this, 'getCanonical')
        ];
    }

    /**
     * Gets the brv url.
     * @return string
     */
    public function getBrvUrl()
    {
        return brv_url();
    }

    /**
     * Gets the canonical path of the current route.
     * @return string
     */
    public function getCanonical()
    {
        $route = \App::getState(\STATES::CURRENT_ROUTE);
        if ($route === null) {
            return null;
        }

        $matches = $route->getMatches();
        $canon = isset($matches[0]) ? $matches[0] : "";
        return brv_url() . $canon;
    }
}
