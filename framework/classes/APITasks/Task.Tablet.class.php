<?php
class TaskTablet extends AbstractTask
{
	private $data;
	
	public function execute($method, $tasks, &$data)
	{
		if($method == 'post'){
			if($data['secure'] !== true || !isset($data['tablet'])){
				throw new Exception("Data integrity compromised.");
			}
		}
		
		$this->data = &$data;
	}
	
	public function taskCommunicate()
	{
		$serial = Brevada::FromPOST('serial');
		$battery_percent = Brevada::FromPOST('battery_percent');
		$battery_plugged_in = Brevada::FromPOST('battery_percent');
		$device_version = Brevada::FromPOST('device_version');
		$device_model = Brevada::FromPOST('device_model');
		$position_latitude = Brevada::FromPOST('position_latitude');
		$position_longitude = Brevada::FromPOST('position_longitude');
		$position_timestamp = Brevada::FromPOST('position_timestamp');
		$stored_data_count = @intval(Brevada::FromPOST('stored_data_count'));
		
		if(empty($serial)){
			throw new Exception("Missing identification.");
		}
		
		$battery_plugged_in = $battery_plugged_in ? 1 : 0;
		
		/* Update tablet. */
		if(($stmt = Database::prepare("
			UPDATE `tablets` SET
			`OnlineSince` = UNIX_TIMESTAMP(NOW()),
			`BatteryPercent` = ?,
			`BatteryPluggedIn` = ?,
			`PositionLatitude` = ?,
			`PositionLongitude` = ?,
			`PositionTimestamp` = ?,
			`StoredDataCount` = ?,
			`DeviceVersion` = ?,
			`DeviceModel` = ?
			WHERE `tablets`.`SerialCode` = ? LIMIT 1
		")) !== false){
			$stmt->bind_param('diddiisss', $battery_percent, $battery_plugged_in, $position_latitude, $position_longitude, $position_timestamp, $stored_data_count, $device_version, $device_model, $serial);
			if($stmt->execute()){
				$this->data['update'] = true;
			} else {
				$this->data['update'] = false;
			}
			$stmt->close();
		}
		
		/* Return relevant commands. */
		$commands = [];
		if(($stmt = Database::prepare("
			SELECT `tablet_commands`.`id`, `tablet_commands`.`Command`
			FROM `tablet_commands` JOIN `tablets` ON `tablets`.`id` = `tablet_commands`.`TabletID`
			WHERE `tablets`.`SerialCode` = ? AND `tablet_commands`.`Received` = 0
		")) !== false){
			$stmt->bind_param('s', $serial);
			if($stmt->execute()){
				$stmt->store_result();
				$stmt->bind_result($cmd_id, $cmd_value);
				while($stmt->fetch()){
					$commands[] = ['id' => $cmd_id, 'command' => $cmd_value];
					Logger::info("Command added.");
				}
			}
			$stmt->close();
		}
		$this->data['commands'] = $commands;
	}
	
	public function taskCommand()
	{
		$serial = Brevada::FromPOST('serial');
		$command_id = Brevada::FromPOST('command_id');
		
		if(empty($command_id)){
			throw new Exception("Missing parameters.");
		}
		
		if(($stmt = Database::prepare("
			UPDATE `tablet_commands`
			SET `tablet_commands`.Received = 1
			WHERE
			`tablet_commands`.`id` = ? AND
			EXISTS (
				SELECT * FROM `tablets`
				WHERE
				`tablets`.`id` = `tablet_commands`.`TabletID` AND
				`tablets`.`SerialCode` = ?
			)
		")) !== false){
			$stmt->bind_param('is', $command_id, $serial);
			if($stmt->execute()){
				$this->data['ok'] = true;
			} else {
				$this->data['ok'] = false;
			}
			$stmt->close();
		}
	}
}
?>