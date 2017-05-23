<?php
/**
 * Marketing | Controller
 *
 * @version v0.0.1 (May 23, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;

/**
 * Marketing Material
 */
class Marketing extends Controller
{

    /**
     * Returns an octet-stream / download of a the marketing press pack.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function download(array $params = [])
    {
        $file = PRIVATE_ASSETS_DIR . "/marketing/BrevadaPressPack.pdf";
        if (!file_exists($file)) {
            self::fail("Bad file.", \HTTP::SERVER);
        }

        return new View([
            "path" => $file
        ], [
            "buffered" => false,
            "type" => "download",
            "headers" => [
                "Content-Description: File Transfer",
                "Content-Type: application/octet-stream",
                "Content-Disposition: attachment; filename=BrevadaPressPack.pdf",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Expires: 0",
                "Content-Length: " . filesize($file)
            ]
        ]);
    }
}
