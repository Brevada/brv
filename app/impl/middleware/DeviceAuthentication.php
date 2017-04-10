<?php
/**
 * Device Authentication | Middleware
 *
 * @version v0.0.1 (Apr. 9, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\middleware;

use Brv\core\routing\Middleware;
use Brv\core\libs\database\Database as DB;

/**
 * Authentication middleware which enforces that the device be authenticated.
 * Completely stateless.
 */
class DeviceAuthentication extends Middleware
{

    /**
     * Creates a view containing a WWW Authentication header.
     * @param  string[] $body The body of the header.
     * @return \Brv\core\views\View|void
     */
    private function createWWWAuthenticate($body)
    {
        return new View(null, [
            "buffered" => false,
            "code" => \HTTP::UNAUTHORIZED,
            "headers" => [
                "WWW-Authenticate: " . implode(',', $body)
            ]
        ]);
    }

    /**
     * Tests validity of token. On success, returns next view.
     * On failure, fails and notifies client.
     *
     * @return \Brv\core\views\View|void
     */
    private function verifyToken($token)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT DeviceUUID, ExpiryDate FROM device_authorization
                WHERE AccessToken = :token LIMIT 1
            ");
            $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $expiry = @intval($row['ExpiryDate']);
                if ($expiry < time()) {
                    /* Report expired. */
                    return $this->createWWWAuthenticate([
                        'Bearer realm="' . HOST . '"',
                        'error="invalid_token"',
                        'error_description="The access token expired"'
                    ]);
                }

                /* Access token is valid and has not expired. */
                \App::setState(\STATES::DEVICE_UUID, $row['DeviceUUID']);
                return $this->getNext();
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return $this->createWWWAuthenticate([
            'Bearer realm="' . HOST . '"',
            'error="invalid_token"',
            'error_description="The access token is invalid"'
        ]);
    }

    /**
     * Gets the next view to process.
     *
     * @return \Brv\core\views\View|void
     */
    public function getView()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            list($type, $token) = array_merge(
                explode(' ', $headers['Authorization']),
                ['', '']
            );

            if (strcasecmp($type, 'bearer') === 0) {
                return $this->verifyToken($token);
            }
        }

        /* Invalid or missing authorization header. */
        return $this->createWWWAuthenticate([
            'Bearer realm="' . HOST . '"'
        ]);
    }
}
