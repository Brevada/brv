<?php
/**
 * Feedback | Controller
 *
 * @version v0.0.1 (Feb. 20, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;
use Brv\impl\entities\Store;
use Brv\impl\entities\Aspect;

use Respect\Validation\Validator as v;

/**
 * The Feedback API.
 */
class Feedback extends Controller
{
    /**
     * Serves the standard feedback gathering view.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function viewFeedback(array $params)
    {
        /* Sanity check. */
        $storeUrl = self::from(1, $params, null);
        if (is_null($storeUrl)) return false;

        /* Load store from URL. */
        $store = Store::queryUrl(trim($storeUrl));
        if (is_null($store)) return false;

        return new View('feedback/standard', [
            'params' => [
                'id' => $store->getId(),
                'name' => $store->getName()
            ]
        ]);
    }

    /**
     * Gets the public configuration settings for a store's feedback page.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getConfig(array $params)
    {
        /* Sanity check. */
        $storeId = self::from('id', $_GET, null);
        v::intVal()->min(0)->check($storeId);

        /* Load store from URL. */
        $store = Store::queryId($storeId);
        if (is_null($store)) {
            self::fail("Invalid store id.", \HTTP::BAD_PARAMS);
        }

        if (!$store->isActive()) {
            self::fail("The store exists but is inactive.");
        }

        $aspects = Aspect::queryStore($store->getId());
        if ($aspects !== null) {
            $aspects = array_values(array_filter($aspects, function ($aspect) {
                return $aspect->isActive();
            }));
        } else {
            $aspects = [];
        }

        return new View([
            'id' => $store->getId(),
            'name' => $store->getName(),
            'url' => $store->getURL(),
            'aspects' => array_map(function ($aspect) {
                return [
                    'id' => $aspect->getId(),
                    'title' => $aspect->getTitle()
                ];
            }, $aspects),
            'template' => $store->getCollectionTemplate(),
            'template_location' => $store->getCollectionLocation(),
            'welcome_message' => $store->getWelcomeMessage(),
            'comment_message' => $store->getCommentMessage(),
            'allow_comments' => $store->isAllowComments()
        ]);
    }
}
