<?php
/**
 * Device | Controller
 *
 * @version v0.0.1 (Apr. 13, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\controllers;

use Brv\core\routing\Controller;
use Brv\core\views\View;
use Brv\impl\entities\Store;
use Brv\impl\entities\Device as EDevice;
use Respect\Validation\Validator as v;

/**
 * The Device API.
 */
class Device extends Controller
{
    /**
     * Gets the time of the request.
     *
     * @param array $payload
     * @return int
     */
    private function getRequestTime($payload)
    {
        $deviceId = \App::getState(\STATES::DEVICE_UUID);
        if ($deviceId === null) {
            return null;
        }

        $time = self::from('_timestamp', $payload);
        if ($time === null) {
            return null;
        }
        $time = (int) $time;

        $SIX_MONTHS = 3600 * 24 * 30 * 6;

        if (!v::intVal()->min(time() - (60 * 15))->max(time() + (60 * 5))->validate($time)) {
            /* Time window slightly out of sync. Just make note. */
            \App::log()->info("Out of sync timestamp for: ${deviceId}.");
            return null;
        }

        return $time;
    }

    /**
     * Updates tablet metadata.
     *
     * @api
     *
     *
     * @param array $params URL parameters from the route pattern.
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     * @return View
     */
    public function announce(array $params)
    {
        $body = self::getBody();
        $deviceId = \App::getState(\STATES::DEVICE_UUID);

        $time = $this->getRequestTime($body);
        if ($time === null || $time < time() - (5 * 60) || $time > time() + 60) {
            self::fail("Invalid timestamp.", \HTTP::BAD_REQUEST);
        }

        $device = EDevice::queryCode($deviceId);

        $device->setOnlineSince(time());
        $device->setIPAddress($_SERVER['REMOTE_ADDR']);

        $battery = self::from('battery', $body);
        if ($battery !== null && is_array($battery)) {
            $percent = self::from('percent', $battery);
            $charging = self::from('charging', $battery);

            if ($percent === null || v::intVal()->min(0)->max(100)->validate($percent)) {
                $device->setBatteryPercent($percent);
            }

            $charging = is_bool($charging) ? $charging : !($charging === 'false');
            $device->setBatteryPluggedIn($charging);
        }

        $position = self::from('position', $body);
        if ($position !== null && is_array($position)) {
            $latitude = self::from('latitude', $position);
            $longitude = self::from('longitude', $position);
            $posTimestamp = self::from('timestamp', $position);

            if ($latitude !== null && $longitude !== null && $posTimestamp !== null) {
                $device->setPositionLatitude($latitude);
                $device->setPositionLongitude($longitude);
                $device->setPositionTimestamp($posTimestamp);
            }
        }

        $storedCount = self::from('stored_data_count', $body);
        if ($storedCount !== null && v::intVal()->validate($storedCount)) {
            $device->setStoredDataCount($storedCount);
        }

        $deviceVersion = self::from('device_version', $body);
        if ($deviceVersion !== null) {
            $device->setDeviceVersion($deviceVersion);
        }

        $deviceModel = self::from('device_model', $body);
        if ($deviceModel !== null) {
            $device->setDeviceModel($deviceModel);
        }

        if ($device->commit() === null) {
            self::fail("Unable to save device information.", \HTTP::SERVER);
        }

        return new View([]);
    }

    /**
     * Gets outstanding commands/actions intended for device.
     *
     * @api
     *
     *
     * @param array $params URL parameters from the route pattern.
     * @throws \Respect\Validation\Exceptions\ValidationException on invalid input.
     * @throws \Brv\core\routing\ControllerException on failure.
     * @return View
     */
    public function getCommands(array $params)
    {
        $deviceId = \App::getState(\STATES::DEVICE_UUID);
        $device = EDevice::queryCode($deviceId);

        $this->getRequestTime($_GET);

        return new View([
            'actions' => $device->getCommands()
        ]);
    }
}
