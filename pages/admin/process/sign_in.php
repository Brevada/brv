<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$id = @intval(Brevada::FromGET('id'));

if(empty($id) || !is_int($id)){ exit('Invalid link.'); }

Logger::info("Account #{$_SESSION['AccountID']} logging in to accounts#{$id}.");

Brevada::Logout();

if(($query = Database::query("SELECT `id`, `EmailAddress`, `CompanyID`, `StoreID`, `Permissions` FROM `accounts` WHERE `id` = {$id} LIMIT 1")) !== false){
	if($query !== false){
		if($query->num_rows > 0){
			while($row = $query->fetch_assoc()){
				if (@intval($row['Permissions']) >= Permissions::VIEW_ADMIN){
					exit('Invalid permissions.');
				}
				
				$_SESSION['AccountID'] = $row['id'];
				$_SESSION['CompanyID'] = empty($row['CompanyID']) ? null : $row['CompanyID'];
				$_SESSION['StoreID'] = empty($row['StoreID']) ? null : $row['StoreID'];
				$_SESSION['Corporate'] = empty($_SESSION['StoreID']);
				$_SESSION['Permissions'] = $row['Permissions'];
			}
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['time'] = time();
		}
	}
}
		
Brevada::Redirect('/dashboard');
?>