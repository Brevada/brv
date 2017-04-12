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
use Brv\core\libs\DeviceCache;

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
        $deviceId = \App::getState(\STATES::DEVICE_UUID);
        if ($deviceId === null) {
            /* Not entering via device route.  */
            $storeId = self::from('id', $_GET, null);
            v::intVal()->min(0)->check($storeId);
            $store = Store::queryId($storeId);
        } else {
            /* Being accessed through device API */
            $store = Store::queryDeviceId($deviceId);
        }

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
            'template_location' => (int) $store->getCollectionLocation(),
            'welcome_message' => $store->getWelcomeMessage(),
            'comment_message' => $store->getCommentMessage(),
            'allow_comments' => $store->isAllowComments()
        ]);
    }

    /**
     *  Gets the current/latest version of the feedback software.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getVersion(array $params = [])
    {
        $version = FEEDBACK_VERSION;
        if ($version === null) {
            $version = exec("git rev-parse --short HEAD");
            if (empty($version)) {
                self::fail("Failed to retrieve version identifier.", \HTTP::SERVER);
            } else {
                /* Clip hash. We just need enough to prevent collisions between
                 * adjacent versions (even 2 would probably work). */
                $version = substr($version, 7);
            }
        }

        return new View([
            "version" => $version
        ]);
    }

    /**
     *  Gets a list of files to download, composing the entire feedback
     *  software package; including all dependencies.
     *
     * Response of the form: { files: [{ id: a, name: a.js }, ...] }
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getBundle(array $params = [])
    {
        $deviceId = \App::getState(\STATES::DEVICE_UUID);

        $cache = self::from('cache', $_GET, true);
        if ($cache === 'false') {
            $cache = false;
        } else if ($cache === 'true') {
            $cache = true;
        } else {
            $cache = (bool) $cache;
        }

        if ($cache && ($bundle = DeviceCache::load($deviceId)) !== null) {
            return new View([
                "files" => $bundle['files']
            ]);
        }

        $files = [];
        $metadata = [];

        /* Prepare list of all dependencies in "feedback" bundle. */
        $deps = glob(NAMESPACE_DIR . "resp/feedback/*.{js,css,json}", GLOB_BRACE);
        foreach($deps as $dep) {
            if (is_dir($dep)) continue;
            if (strpos($dep, '.') === 0) continue;

            $id = bin2hex(openssl_random_pseudo_bytes(8));
            $name = basename($dep);

            $files[] = [
                "id" => $id,
                "name" => $name
            ];

            $metadata[$id] = $dep;
        }

        /* Save id map. */
        DeviceCache::save($deviceId, [
            "files" => $files,
            "metadata" => $metadata
        ]);

        return new View([
            "files" => $files
        ]);
    }

    /**
     * Returns an octet-stream / download of a single requested file. The file
     * identifier must have been previously generated by a call to get the bundle.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function getBundleItem(array $params = [])
    {
        /* Check session and send file from session. */
        $deviceId = \App::getState(\STATES::DEVICE_UUID);
        $bundle = DeviceCache::load($deviceId);
        if (is_null($bundle)) self::fail("No bundle initialized.", \HTTP::BAD_REQUEST);

        if (!isset($bundle['metadata'][$params[1]])) {
            self::fail("Invalid bundle item idenitifer.", \HTTP::BAD_PARAMS);
        }

        $file = $bundle['metadata'][$params[1]];
        if (!file_exists($file)) {
            self::fail("Bad bundle.", \HTTP::SERVER);
        }

        return new View([
            "path" => $file
        ], [
            "buffered" => false,
            "type" => "download",
            "headers" => [
                "Content-Description: File Transfer",
                "Content-Type: application/octet-stream",
                "Content-Disposition: attachment; filename={$params[1]}",
                "Cache-Control: must-revalidate",
                "Content-Length: " . filesize($file)
            ]
        ]);
    }
}
