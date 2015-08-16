<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$serial = strtolower(trim(Brevada::FromPOST('txtSerial')));

if(empty($serial)){ Brevada::Redirect('/admin?show=tablets&error'); }

$alreadyExists = false;

if(($stmt = Database::query("SELECT `tablets`.`SerialCode` FROM `tablets` WHERE `tablets`.`SerialCode` = ? LIMIT 1")) !== false){
	$stmt->bind_param('s', $serial);
	if($stmt->execute()){
		$stmt->store_result();
		if($stmt->num_rows > 0){
			$alreadyExists = true;
		}
	}
	$stmt->close();
}

if($alreadyExists){ Brevada::Redirect('/admin?show=tablets&error'); }

if(($stmt = Database::prepare("INSERT INTO `tablets` (`SerialCode`, `StoreID`) VALUES (?, NULL)")) !== false){
	$stmt->bind_param('s', $serial);
	if(!$stmt->execute()){
		Brevada::Redirect('/admin?show=tablets&error');
	}
	$stmt->close();
	
	Logger::info("Account #{$_SESSION['AccountID']} registered a new tablet with serial: {$serial}");
}

Brevada::Redirect('/admin?show=tablets');
?>