<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$fTo = trim(Brevada::FromPOST('txtTo'));
$fType = trim(Brevada::FromPOST('ddType'));
$fSilent = trim(Brevada::FromPOST('chkSilent'));
$fTitle = trim(Brevada::FromPOST('txtTitle'));
$fDescription = trim(Brevada::FromPOST('txtDescription'));

if(empty($fTo) || empty($fType) || empty($fTitle)){
	Brevada::Redirect('/admin?show=messages&failed=1');
}

$params = [
	'to' => $fTo,
	'type' => $fType,
	'silent' => !empty($fSilent),
	'title' => $fTitle,
	'description' => $fDescription
];

if(($result = Notification::create($params)) !== false){
	Logger::info("Account #{$_SESSION['AccountID']} sent a broadcast message.");
	Brevada::Redirect('/admin?show=messages&sent');
	
} else {
	Logger::info("Account #{$_SESSION['AccountID']} failed to send a broadcast message.");
	Brevada::Redirect('/admin?show=messages&failed=1');
}
?>