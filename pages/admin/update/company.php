<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$id = @intval(Brevada::FromPOST('id'));
$column = @intval(Brevada::FromPOST('column'));
$value = Brevada::FromPOST('value');

$columns = array(false, 'Name', 'PhoneNumber');

if(empty($id) || empty($column) || $column < 0 || $column > count($columns) || $columns[$column] == false){ exit('Invalid'); }

if(($stmt = Database::prepare("UPDATE `companies` SET `{$columns[$column]}` = ? WHERE `id` = {$id}")) !== false){
	$stmt->bind_param('s', $value);
	if(!$stmt->execute()){
		$stmt->close();
		exit('Invalid');
	}
	$stmt->close();
}

exit('OK');
?>