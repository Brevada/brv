<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$tablet = @intval(Brevada::FromPOST('id'));
$command = trim(Brevada::FromPOST('command'));

if(empty($tablet) || empty($command)){ Brevada::Redirect('/admin?show=tablets&error'); }

if(($stmt = Database::prepare("INSERT INTO `tablet_commands` (`TabletID`, `Command`, `DateIssued`) VALUES (?, ?, UNIX_TIMESTAMP(NOW()))")) !== false){
	$stmt->bind_param('is', $tablet, $command);
	if(!$stmt->execute()){
		Brevada::Redirect('/admin?show=tablets&error');
	}
	$stmt->close();
	
	Logger::info("Account #{$_SESSION['AccountID']} sent '{$command}' to tablet#{$tablet}.");
}

Brevada::Redirect('/admin?show=tablets&sent=1&id='.$tablet);
?>