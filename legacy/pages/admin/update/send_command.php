<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$tablet = @intval(Brevada::FromPOST('id'));
$command = trim(Brevada::FromPOST('command'));

if(empty($tablet) || empty($command)){ Brevada::Redirect('/admin?show=tablets&error'); }

if(!Tablet::ExecuteById($tablet, $command)){
	Brevada::Redirect('/admin?show=tablets&error');
}

Brevada::Redirect('/admin?show=tablets&sent=1&id='.$tablet);
?>