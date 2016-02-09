<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$password = trim(Brevada::FromPOST('password'));

if(strlen($password) < 8){ exit('Invalid password length.'); }

$password = Brevada::HashPassword($password);
if(($stmt = Database::prepare("UPDATE `accounts` SET `Password` = ? WHERE `id` = ? LIMIT 1")) !== false){
	$accountID = $_SESSION['AccountID'];
	$stmt->bind_param('si', $password, $accountID);
	$stmt->execute();
	$stmt->close();
}

exit('OK');
?>