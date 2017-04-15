<?php
/**
 * Device | Entity
 *
 * @version v0.0.1 (Apr. 13, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities;

use Brv\core\entities\Entity;
use Brv\core\libs\database\Database as DB;

/**
 * An entity representing a device such as a tablet, intended for
 * gathering feedback data.
 */
class Device extends Entity
{
    use common\IPAddress,
        common\StoreId;

    /**
     * Instantiates a device entity from a data row.
     *
     * @param array $row The data row from which to hydrate from.
     */
    public function __construct(array $row = [])
    {
        $this->hydrate($row, Entity::HYDRATE_ALL);
    }

    /* Query Functions */

    /**
     * Factory method to instantiate a device entity from an id.
     *
     * @param integer $id
     * @return self
     */
    public static function queryId($id)
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT
                    id, SerialCode, StoreID, Status, OnlineSince,
                    IPAddress, BatteryPercent, BatteryPluggedIn,
                    PositionLatitude, PositionLongitude, PositionTimestamp,
                    StoredDataCount, DeviceVersion, DeviceModel
                FROM tablets WHERE id = :id
            ");
            $stmt->bindValue(':id', (int) $id, \PDO::PARAM_INT);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([])->hydrate($row);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Factory method to instantiate a device entity from a device UUID (serial
     * code).
     *
     * @param string $code
     * @return self
     */
    public static function queryCode($code)
    {
        try {
            $stmt = DB::get()->prepare("
            SELECT
                id, SerialCode, StoreID, Status, OnlineSince,
                IPAddress, BatteryPercent, BatteryPluggedIn,
                PositionLatitude, PositionLongitude, PositionTimestamp,
                StoredDataCount, DeviceVersion, DeviceModel
            FROM tablets WHERE SerialCode = :code
            ");
            $stmt->bindValue(':code', $code, \PDO::PARAM_STR);
            $stmt->execute();
            if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
                return self::from([])->hydrate($row);
            }
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /* Instance Methods */

    /**
     * Commits device to database.
     *
     * @return integer The id of the newly created/updated device.
     */
    public function commit()
    {
        try {
            $stmt = DB::get()->prepare("
                INSERT INTO tablets
                    (SerialCode, StoreID, Status, OnlineSince,
                    IPAddress, BatteryPercent, BatteryPluggedIn,
                    PositionLatitude, PositionLongitude, PositionTimestamp,
                    StoredDataCount, DeviceVersion, DeviceModel)
                VALUES
                    (:SerialCode, :StoreID, :Status, :OnlineSince,
                    :IPAddress, :BatteryPercent, :BatteryPluggedIn,
                    :PositionLatitude, :PositionLongitude, :PositionTimestamp,
                    :StoredDataCount, :DeviceVersion, :DeviceModel)
                ON DUPLICATE KEY UPDATE
                    StoreID = :StoreID, Status = :Status, OnlineSince = :OnlineSince,
                    IPAddress = :IPAddress, BatteryPercent = :BatteryPercent,
                    BatteryPluggedIn = :BatteryPluggedIn,
                    PositionLatitude = :PositionLatitude,
                    PositionLongitude = :PositionLongitude,
                    PositionTimestamp = :PositionTimestamp,
                    StoredDataCount = :StoredDataCount,
                    DeviceVersion = :DeviceVersion, DeviceModel = :DeviceModel
            ");

            $stmt->bindValue(':StoreID', $this->getStoreId(), \PDO::PARAM_INT);
            $stmt->bindValue(':Status', $this->getStatus(), \PDO::PARAM_STR);
            $stmt->bindValue(':OnlineSince', $this->getOnlineSince(), \PDO::PARAM_INT);
            $stmt->bindValue(':IPAddress', $this->getIPAddress(), \PDO::PARAM_STR);
            $stmt->bindValue(':BatteryPercent', $this->getBatteryPercent(), \PDO::PARAM_STR);
            $stmt->bindValue(':BatteryPluggedIn', (int) $this->isBatteryPluggedIn(), \PDO::PARAM_INT);
            $stmt->bindValue(':PositionLatitude', $this->getPositionLatitude(), \PDO::PARAM_STR);
            $stmt->bindValue(':PositionLongitude', $this->getPositionLongitude(), \PDO::PARAM_STR);
            $stmt->bindValue(':PositionTimestamp', $this->getPositionTimestamp(), \PDO::PARAM_INT);
            $stmt->bindValue(':StoredDataCount', $this->getStoredDataCount(), \PDO::PARAM_INT);
            $stmt->bindValue(':DeviceVersion', $this->getDeviceVersion(), \PDO::PARAM_STR);
            $stmt->bindValue(':DeviceModel', $this->getDeviceModel(), \PDO::PARAM_STR);
            $stmt->bindValue(':SerialCode', $this->getSerialCode(), \PDO::PARAM_STR);

            $stmt->execute();
            $this->set('id', (int) DB::get()->lastInsertId());

            return $this->getId();
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return null;
    }

    /**
     * Gets all outstanding commands.
     *
     * @return array
     */
    public function getCommands()
    {
        try {
            $stmt = DB::get()->prepare("
                SELECT id, Command FROM tablet_commands
                WHERE TabletID = :id AND Received = 0
                ORDER BY DateIssued ASC
            ");
            $stmt->bindValue(':id', $this->getId(), \PDO::PARAM_INT);
            $stmt->execute();
            $commands = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = DB::get()->prepare("
                UPDATE tablet_commands SET Received = 1 WHERE id = :id
            ");

            foreach ($commands as $cmd) {
                $stmt->bindValue(':id', $cmd['id'], \PDO::PARAM_INT);
                $stmt->execute();
            }

            return array_values(array_map(function ($v) {
                return $v['Command'];
            }, $commands));
        } catch (\PDOException $ex) {
            \App::log()->error($ex->getMessage());
        }

        return [];
    }

    public function setStatus($v) {
        $this->set('Status', $v);
        return $this->getStatus();
    }

    public function getStatus() {
        return $this->get('Status');
    }

    public function setOnlineSince($v) {
        $this->set('OnlineSince', is_null($v) ? null : (int) $v);
        return $this->getOnlineSince();
    }

    public function getOnlineSince() {
        return $this->get('OnlineSince') === null ? null : (int) $this->get('OnlineSince');
    }

    public function setBatteryPercent($v) {
        $this->set('BatteryPercent', $v);
        return $this->getBatteryPercent();
    }

    public function getBatteryPercent() {
        return $this->get('BatteryPercent');
    }

    public function setBatteryPluggedIn($v) {
        $this->set('BatteryPluggedIn', is_null($v) ? $v : (int) $v);
        return $this->isBatteryPluggedIn();
    }

    public function isBatteryPluggedIn() {
        return ((int) $this->get('BatteryPluggedIn')) === 1;
    }

    public function setPositionLatitude($v) {
        $this->set('PositionLatitude', $v);
        return $this->getPositionLatitude();
    }

    public function getPositionLatitude() {
        return $this->get('PositionLatitude');
    }

    public function setPositionLongitude($v) {
        $this->set('PositionLongitude', $v);
        return $this->getPositionLongitude();
    }

    public function getPositionLongitude() {
        return $this->get('PositionLongitude');
    }

    public function setPositionTimestamp($v) {
        $this->set('PositionTimestamp', is_null($v) ? null : (int) $v);
        return $this->getPositionTimestamp();
    }

    public function getPositionTimestamp() {
        return $this->get('PositionTimestamp') === null ? null : (int) $this->get('PositionTimestamp');
    }

    public function setStoredDataCount($v) {
        $this->set('StoredDataCount', (int) $v);
        return $this->getStoredDataCount();
    }

    public function getStoredDataCount() {
        return (int) $this->get('StoredDataCount');
    }

    public function setDeviceVersion($v) {
        $this->set('DeviceVersion', $v);
        return $this->getDeviceVersion();
    }

    public function getDeviceVersion() {
        return $this->get('DeviceVersion');
    }

    public function setDeviceModel($v) {
        $this->set('DeviceModel', $v);
        return $this->getDeviceModel();
    }

    public function getDeviceModel() {
        return $this->get('DeviceModel');
    }

    public function setSerialCode($v) {
        $this->set('SerialCode', $v);
        return $this->getSerialCode();
    }

    public function getSerialCode() {
        return $this->get('SerialCode');
    }
}
