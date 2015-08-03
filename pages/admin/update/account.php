<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$id = @intval(Brevada::FromPOST('id'));
$column = @intval(Brevada::FromPOST('column'));
$value = Brevada::FromPOST('value');

$columns = array(false, 'FirstName', 'LastName', 'EmailAddress', 'Password', 'Company', 'Store', 'Permissions');

if(empty($id) || empty($column) || $column < 0 || $column > count($columns) || $columns[$column] == false){ exit('Invalid'); }

if($columns[$column] == 'Company'){
	$value = @intval($value);
	if(($query = Database::query("UPDATE `accounts` SET `CompanyID` = {$value} WHERE `accounts`.`id` = {$id}")) !== false){
		if(($query = Database::query("SELECT `companies`.`Name` FROM `companies` WHERE `companies`.`id` = {$value} LIMIT 1")) !== false){
			if($query->num_rows == 0){ exit('Invalid'); }
			while($row = $query->fetch_assoc()){
				exit($row['Name']);
			}
		}
	} else { exit('Invalid'); }
} else if($columns[$column] == 'Store'){
	if($value == '' || strtolower($value) == 'null'){
		if(($query = Database::query("UPDATE `accounts` SET `StoreID` = NULL WHERE `accounts`.`id` = {$id}")) !== false){
			exit('null');
		} else { exit('Invalid'); }
	} else {
		$value = @intval($value);
		if(($query = Database::query("UPDATE `accounts` SET `StoreID` = {$value} WHERE `accounts`.`id` = {$id}")) !== false){
			if(($query = Database::query("SELECT `stores`.`Name` FROM `stores` WHERE `stores`.`id` = {$value} LIMIT 1")) !== false){
				if($query->num_rows == 0){ exit('Invalid'); }
				while($row = $query->fetch_assoc()){
					exit($row['Name']);
				}
			}
		} else { exit('Invalid'); }
	}
} else {
	if(($stmt = Database::prepare("UPDATE `accounts` SET `{$columns[$column]}` = ? WHERE `id` = {$id}")) !== false){
		if($columns[$column] == 'Permissions'){
			$stmt->bind_param('i', $value);
		} else {
			$stmt->bind_param('s', $value);
		}
		if(!$stmt->execute()){
			$stmt->close();
			exit('Invalid');
		}
		$stmt->close();
	}
}

exit('OK');
?>