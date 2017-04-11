<?php
/**
 * Device Cache
 *
 * @version v0.0.1 (Apr. 10, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\core\libs;

/**
 * Responsible for short term caching of data for devices (such as tablets),
 * utilizing Brevada software.
 */
class DeviceCache
{
    /**
     * Gets the path to the device cache given a device id.
     * @param  string $deviceId
     * @return string
     */
    public static function getPath($deviceId)
    {
        return NAMESPACE_DIR . "resp/device_cache/" . sha1($deviceId) . '.json';
    }

    /**
     * Loads the device cache into memory from a device id.
     * @param  string $deviceId
     * @return array
     */
    public static function load($deviceId)
    {
        $path = self::getPath($deviceId);
        if (file_exists($path)) {
            $data = file_get_contents($path);
            if (!$data) return null;
            $data = @json_decode($data, true);
            return $data;
        }

        return null;
    }

    /**
     * Saves the device cache from memory to disk for a device id.
     * @param  string $deviceId
     * @param array $data The data to store.
     */
    public static function save($deviceId, $data)
    {
        $path = self::getPath($deviceId);
        file_put_contents($path, json_encode($data));
    }
}
