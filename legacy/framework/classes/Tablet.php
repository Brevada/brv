<?php
class Tablet
{
	public static function ExecuteByID($id, $command)
	{
		if(($stmt = Database::prepare("
			INSERT INTO `tablet_commands` (`TabletID`, `Command`, `DateIssued`) 
			SELECT ? as `TabletID`, ? as `Command`, UNIX_TIMESTAMP() as `DateIssued`
			FROM tablets
			WHERE tablets.id = ? AND NOT EXISTS (
				SELECT 1 FROM `tablet_commands` tb 
				WHERE tb.Received = 0 AND tb.TabletID = tablets.id AND tb.Command = ?
			)
		")) !== false){
			$stmt->bind_param('isis', $id, $command, $id, $command);
			if(!$stmt->execute()){
				return false;
			}
			$stmt->close();
			
			Logger::info("Account #{$_SESSION['AccountID']} sent '{$command}' to tablet#{$id}.");
			
			return true;
		}
		return false;
	}
	
	public static function ExecuteByStore($id, $command)
	{
		if(($stmt = Database::prepare("
			INSERT INTO `tablet_commands` (`TabletID`, `Command`, `DateIssued`)
			SELECT tablets.id as `TabletID`, ? as `Command`, UNIX_TIMESTAMP() as `DateIssued`
			FROM tablets
			WHERE tablets.StoreID = ? AND NOT EXISTS (
				SELECT 1 FROM `tablet_commands` tb 
				WHERE tb.Received = 0 AND tb.TabletID = tablets.id AND tb.Command = ?
			)
		")) !== false){
			$stmt->bind_param('sis', $command, $id, $command);
			if(!$stmt->execute()){
				return false;
			} else {
				Logger::info("Account #{$_SESSION['AccountID']} sent '{$command}' to {$stmt->num_rows} tablets by store#{$id}.");
			}
			$stmt->close();
			
			return true;
		}
		return false;
	}
	
	public static function RestartByStore($id)
	{
		return self::ExecuteByStore($id, "restart");
	}
}
?>