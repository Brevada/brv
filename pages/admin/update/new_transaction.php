<?php
$this->IsScript = true;

if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$id = @intval(Brevada::FromPOST('id')); /* company id */

$months = @intval(strtolower(trim(Brevada::FromPOST('txtMonths'))));
$stores = @intval(strtolower(trim(Brevada::FromPOST('txtStores'))));
$tablets = @intval(strtolower(trim(Brevada::FromPOST('txtTablets'))));
$logins = @intval(strtolower(trim(Brevada::FromPOST('txtLogins'))));

if(empty($id) || empty($months)){ Brevada::Redirect('/admin?show=companies&error'); }

if(($stmt = Database::query("SELECT `companies`.`id` FROM `companies` WHERE `companies`.`id` = ? LIMIT 1")) !== false){
	$stmt->bind_param('i', $id);
	if($stmt->execute()){
		$stmt->store_result();
		if($stmt->num_rows == 0){
			$stmt->close();
			Brevada::Redirect('/admin?show=companies&error');
		}
	}
	$stmt->close();
}

if(($stmt = Database::prepare("INSERT INTO `transactions` (`Date`, `CompanyID`, `Value`, `Currency`, `Product`, `Confirmed`) VALUES (NOW(), ?, 0, 'CAD', 'Brevada Custom Package', 1)")) !== false){
	$stmt->bind_param('i', $id);
	if(!$stmt->execute()){
		Brevada::Redirect('/admin?show=companies&error');
	}
	$stmt->close();
	
	Logger::info("Account #{$_SESSION['AccountID']} created a new free transaction for companies.id#{$id}.");
}

$check = Database::query("SELECT companies.FeaturesID FROM companies WHERE companies.id = {$id} AND `companies`.FeaturesID IS NOT NULL LIMIT 1");
if($check !== false && $check->num_rows == 0){
	Database::query("INSERT INTO `company_features` (`MaxAccounts`, `MaxStores`, `MaxTablets`) VALUES (0, 0, 0)");
	$featuresID = Database::getCon()->insert_id;
	Database::query("UPDATE `companies` SET `companies`.FeaturesID = {$featuresID} WHERE `companies`.`id` = {$id}");
}

Database::query("UPDATE `company_features` LEFT JOIN `companies` ON `companies`.`FeaturesID` = `company_features`.`id` SET `MaxAccounts` = `MaxAccounts` + {$logins}, `MaxStores` = `MaxStores` + {$stores}, `MaxTablets` = `MaxTablets` + {$tablets} WHERE `companies`.`id` = {$id}");

Database::query("UPDATE `companies` SET `Active` = 1, `ExpiryDate` = DATE_ADD(`ExpiryDate`, INTERVAL {$months} MONTH) WHERE `companies`.`id` = {$id}");

Brevada::Redirect('/admin?show=companies');
?>