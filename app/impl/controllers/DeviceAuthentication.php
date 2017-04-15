<?php
/**
 * Device Authentication | Controller
 *
 * @version v0.0.1 (Apr. 9, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;
use Brv\core\libs\database\Database as DB;
use Brv\impl\middleware\Authentication as MiddleAuth;
use Brv\impl\entities\Store as EStore;

use Respect\Validation\Validator as v;

/**
 * The Device Authentication API.
 */
class DeviceAuthentication extends Controller
{
    /**
     * Issues a request token to a specific store via a GET request.
     *
     * NOTE: This is a temporary workaround until the settings system is implemented.
     * As a result, this WILL be restricted to admin accounts only.
     *
     * @api
     * @deprecated
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function issueRequestTokenT(array $params)
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        if ($account === null || $account->getLegacyPermissions() != 255) {
            self::fail("You do not have the necessary permissions to perform this action.", \HTTP::FORBIDDEN);
        }

        $storeId = self::from('store', $_GET, null);
        if ($storeId === null) {
            self::fail("A store must be specified.");
        }
        v::intVal()->min(0)->check($storeId);

        return $this->issueRequestToken($params, $storeId);
    }

    /**
     * Issues a request token to a specific store.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @param integer $storeId Allow store id to be passed into function.
     * @return View
     */
    public function issueRequestToken(array $params, $storeId = null)
    {
        /* Defined account is a precondition due to middleware. */
        $account = MiddleAuth::get();

        if ($storeId === null) {
            $storeId = self::from('store', self::getBody(), null);
            if ($storeId === null) {
                self::fail("A store must be specified.");
            }
            v::intVal()->min(0)->check($storeId);
        }

        /* Check WRITE permissions for store. */
        $store = EStore::queryId($storeId);
        if ($store === null || !$account->getPermissions($store)->canWrite()) {
            self::fail("Store is invalid or missing necessary permissions.", \HTTP::BAD_PARAMS);
        }

        $requestToken = null;

        try {
            $attempts = 0;
            do {
                /* Generates a random token of form: XXXX-XXXX-XXXX. */
                $requestToken = implode('-', str_split(bin2hex(openssl_random_pseudo_bytes(6)), 4));

                $stmt = DB::get()->prepare("
                    INSERT INTO device_request_tokens
                    SET RequestToken = :token, StoreID = :storeId,
                    ExpiryDate = UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL 3 DAY))
                ");
                $stmt->bindValue(':storeId', $store->getId(), \PDO::PARAM_INT);
                $stmt->bindValue(':token', $requestToken, \PDO::PARAM_STR);
                $stmt->execute();

                $attempts++;

                if ($attempts > 5) {
                    \App::log()->error("Request token generation taking more than ${attempts} attempts.");
                    self::fail("Error generating request token.", \HTTP::SERVER);
                }
            } while ($stmt->rowCount() === 0);
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
            self::fail("Issue generating request token.", \HTTP::SERVER);
        }

        if ($requestToken === null) {
            self::fail("Issue generating request token.", \HTTP::SERVER);
        }

        return new View([
            "request_token" => strtoupper($requestToken)
        ]);
    }

    /**
     * Consumes a request token, returning the associated store id.
     *
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param  string $requestToken
     * @return integer
     */
    private function consumeRequest($requestToken)
    {
        /* Change "_" to "-". Mainly for ease of entry, doesn't really reduce
         * entropy since we are only generating tokens with alphanumeric chars. */
        $storeId = null;

        try {
            /* Attempt to "consume" request token. */
            $stmt = DB::get()->prepare("
                SELECT StoreID FROM `device_request_tokens` WHERE
                (`RequestToken` = :token OR `RequestToken` = :token_alt)
                AND `ExpiryDate` > UNIX_TIMESTAMP(NOW())
            ");
            $stmt->bindValue(':token', $requestToken, \PDO::PARAM_STR);
            $stmt->bindValue(':token_alt', str_replace('_', '-', $requestToken), \PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $storeId = @intval($row['StoreID']);
            } else {
                self::fail("Invalid request token.", \HTTP::FORBIDDEN);
            }

            $stmt = DB::get()->prepare("
                DELETE FROM `device_request_tokens`
                WHERE
                `RequestToken` = :token OR
                `RequestToken` = :token_alt OR
                `ExpiryDate` < UNIX_TIMESTAMP(NOW())
            ");
            $stmt->bindValue(':token', $requestToken, \PDO::PARAM_STR);
            $stmt->bindValue(':token_alt', str_replace('_', '-', $requestToken), \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
            self::fail("Issue validating request token.", \HTTP::SERVER);
        }

        return $storeId;
    }

    /**
     * Associates a device id with a store id.
     *
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param  string $requestToken
     */
    private function associateStore($deviceId, $storeId)
    {
        /* INSERT / UPDATE tablet row. */
        try {
            $stmt = DB::get()->prepare("
                INSERT INTO tablets (SerialCode, StoreID) VALUES (:deviceId, :storeId)
                ON DUPLICATE KEY UPDATE StoreID = :storeId
            ");
            $stmt->bindValue(':deviceId', $deviceId, \PDO::PARAM_STR);
            $stmt->bindValue(':storeId', $storeId, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
            self::fail("Issue registering device.", \HTTP::SERVER);
        }
    }

    /**
     * Issues an access token for a device with a specified expiry date.
     *
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param  string $deviceId The UUID of the device.
     * @param integer $expiry Expiry date (in unix time) of the token.
     * @return string The access token.
     */
    private function issueAccessToken($deviceId, $expiry)
    {
        $accessToken = null;

        try {
            $attempts = 0;
            do {
                /* Generates a random token. */
                $accessToken = bin2hex(openssl_random_pseudo_bytes(12));

                $stmt = DB::get()->prepare("
                    INSERT INTO device_authorization
                    SET AccessToken = :token, DeviceUUID = :deviceId,
                    ExpiryDate = :expiry
                ");
                $stmt->bindValue(':deviceId', $deviceId, \PDO::PARAM_STR);
                $stmt->bindValue(':token', $accessToken, \PDO::PARAM_STR);
                $stmt->bindValue(':expiry', $expiry, \PDO::PARAM_INT);
                $stmt->execute();

                $attempts++;

                if ($attempts > 5) {
                    \App::log()->error("Access token generation taking more than ${attempts} attempts.");
                    self::fail("Error generating access token.", \HTTP::SERVER);
                }
            } while ($stmt->rowCount() === 0);
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
            self::fail("Issue registering device.", \HTTP::SERVER);
        }

        return $accessToken;
    }

    /**
     * Renews an access token with a specified expiry date.
     *
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param  string $deviceId The UUID of the device.
     * @param  string $accessToken The access token to renew.
     * @param integer $expiry Expiry date (in unix time) of the token.
     * @return string The new access token.
     */
    private function renewAccessToken($deviceId, $accessToken, $expiry)
    {
        try {
            /* Delete old token if not expired. */
            $stmt = DB::get()->prepare("
                DELETE FROM device_authorization WHERE
                AccessToken = :token AND DeviceUUID = :deviceId
                AND ExpiryDate < UNIX_TIMESTAMP(NOW())
            ");
            $stmt->bindValue(':token', $accessToken, \PDO::PARAM_STR);
            $stmt->bindValue(':deviceId', $deviceId, \PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                /* Client must renew token BEFORE it expires, otherwise
                 * the client must register for a new token. */
                self::fail("Invalid access token. You may not renew an expired token.", \HTTP::FORBIDDEN);
            }

            $accessToken = null;

            /* Generate a new token. */
            $attempts = 0;
            do {
                /* Generates a random token. */
                $accessToken = bin2hex(openssl_random_pseudo_bytes(12));

                $stmt = DB::get()->prepare("
                    INSERT INTO device_authorization
                    SET AccessToken = :token, DeviceUUID = :deviceId,
                    ExpiryDate = :expiry
                ");
                $stmt->bindValue(':deviceId', $deviceId, \PDO::PARAM_STR);
                $stmt->bindValue(':token', $accessToken, \PDO::PARAM_STR);
                $stmt->bindValue(':expiry', $expiry, \PDO::PARAM_INT);
                $stmt->execute();

                $attempts++;

                if ($attempts > 5) {
                    \App::log()->error("Access token generation taking more than ${attempts} attempts.");
                    self::fail("Error generating access token.", \HTTP::SERVER);
                }
            } while ($stmt->rowCount() === 0);

            return $accessToken;
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
            self::fail("Issue registering device.", \HTTP::SERVER);
        }
    }

    /**
     * Registers a device and issues an access_token.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function register(array $params)
    {
        $data = self::getBody();

        $deviceId = self::from('device_id', $data);
        $requestToken = self::from('request_token', $data);

        if ($deviceId === null || $requestToken === null) {
            self::fail("Request parameters are required.", \HTTP::BAD_PARAMS);
        }

        $storeId = $this->consumeRequest($requestToken);
        $this->associateStore($deviceId, $storeId);

        /* Generate access_token. */
        $expiry = time() + (86400 * 30 * 4);
        $renewal_date = time() + (86400 * 30 * 2);
        $accessToken = $this->issueAccessToken($deviceId, $expiry);

        return new View([
            "access_token" => $accessToken,
            "expiry_date" => $expiry,
            "renewal_date" => $renewal_date /* recommend renewal date */
        ]);
    }

    /**
     * Deletes and re-issues an access token, thus extending the expiry.
     * This renders the previous token null and void.
     *
     * @api
     *
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     *
     * @param array $params URL parameters from the route pattern.
     * @return View
     */
    public function renew(array $params)
    {
        $data = self::getBody();

        $deviceId = self::from('device_id', $data);
        $accessToken = self::from('access_token', $data);

        if ($deviceId === null || $accessToken === null) {
            self::fail("Renewal parameters are required.", \HTTP::BAD_PARAMS);
        }

        /* Renew access_token. */
        $expiry = time() + (86400 * 30 * 4);
        $renewal_date = time() + (86400 * 30 * 2);
        $accessToken = $this->renewAccessToken($deviceId, $accessToken, $expiry);

        return new View([
            "access_token" => $accessToken,
            "expiry_date" => $expiry,
            "renewal_date" => $renewal_date /* recommend renewal date */
        ]);
    }
}
