<?php
class Notification
{
	public static function create($params)
	{
		$to = isset($params['to']) ? $params['to'] : '';
		$title = isset($params['title']) ? $params['title'] : '';
		$description = isset($params['description']) ? $params['description'] : '';
		$type = isset($params['type']) ? @intval($params['type']) : '';
		$silent = isset($params['silent']) && $params['silent'] ? 1 : 0;

		if(empty($to) || empty($title) || empty($type)){
			return false;
		}
		
		$toWhere = "";

		if($to == '*'){
			$toWhere = "";
		} else {
			$tos = explode(',', $to);
			foreach($tos as $t){
				if(!is_numeric($t)){
					return false;
				}
			}
			$toWhere = "`accounts`.`id` IN (".implode(',', $tos).") AND ";
		}

		if(($stmt = Database::prepare("INSERT INTO `notifications` (`ToAccount`, `NType`, `Silent`, `Title`, `Description`, `Date`) SELECT `accounts`.`id`, `notification_type`.`id`, ?, ?, ?, NOW() FROM `accounts` JOIN `notification_type` ON `notification_type`.`id` = ? WHERE {$toWhere} `accounts`.`Permissions` < ".Permissions::EDIT_ADMIN)) !== false){
			$stmt->bind_param('issi', $silent, $title, $description, $fType);
			if(!$stmt->execute()){
				$stmt->close();
				return false;
			} else {
				$stmt->close();
			}
		}
		
		return true;
	}
}
?>