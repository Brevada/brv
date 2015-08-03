<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$id = @intval(Brevada::FromPOST('id'));
$column = @intval(Brevada::FromPOST('column'));
$value = Brevada::FromPOST('value');

$columns = array(false, 'SerialCode', 'StoreName', 'Status');

if(empty($id) || empty($column) || $column < 0 || $column > count($columns) || $columns[$column] == false){ exit('Invalid'); }

if($columns[$column] == 'StoreName'){
	$value = @intval($value);
	if(($query = Database::query("UPDATE `tablets` SET `StoreID` = {$value} WHERE `tablets`.`id` = {$id}")) !== false){
		if(($query = Database::query("SELECT `stores`.`Name` FROM `stores` WHERE `stores`.`id` = {$value} LIMIT 1")) !== false){
			if($query->num_rows == 0){ exit('Invalid'); }
			while($row = $query->fetch_assoc()){
				exit($row['Name']);
			}
		}
	} else { exit('Invalid'); }
} else {
	if(($stmt = Database::prepare("UPDATE `tablets` SET `{$columns[$column]}` = ? WHERE `id` = {$id}")) !== false){
		$stmt->bind_param('s', $value);
		if(!$stmt->execute()){
			$stmt->close();
			exit('Invalid');
		}
		$stmt->close();
	}
}

exit('OK');
?>