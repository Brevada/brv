<?php
$this->IsScript = true;

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/logout');
}

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}

if(isset($_POST['txtEmailAddress']) && isset($_POST['ddStores'])){
	$email = trim(strtolower(Brevada::FromPOST('txtEmailAddress')));
	$storeID = @intval(Brevada::FromPOST('ddStores'));
	
	if(empty($email)){ Brevada::Redirect('/settings/settings?section=logins&error=1'); }
	
	$numAccounts = 0;
	$maxAccounts = 1;

	if(($query = Database::query("SELECT `company_features`.`MaxAccounts` FROM `company_features` LEFT JOIN `companies` ON `companies`.FeaturesID = `company_features`.id WHERE `companies`.id = {$_SESSION['CompanyID']} LIMIT 1"))!==false){
		$maxAccounts = @intval($query->fetch_assoc()['MaxAccounts']);
	}

	if($maxAccounts == 0){ $maxAccounts = 1; }

	if(($query = Database::query("SELECT `accounts`.id as AccountID, `accounts`.`EmailAddress`, `accounts`.`StoreID`, `stores`.`Name` as StoreName, `accounts`.`Permissions` FROM `accounts` LEFT JOIN `stores` ON `stores`.id = `accounts`.StoreID WHERE `accounts`.`CompanyID` = {$_SESSION['CompanyID']} ORDER BY `EmailAddress` ASC")) !== false){
		$numAccounts = $query->num_rows;
	}
	
	if($numAccounts < $maxAccounts){
		if($storeID > 0){
			$check = Database::query("SELECT `stores`.`id` FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} AND `stores`.`id` = {$storeID} LIMIT 1");
			if($check === false || $check->num_rows == 0){
				Brevada::Redirect('/settings/settings?section=logins&error=1');
			}
		}
	
		/* Check validity of email. */
		$validity_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		
		if($validity_email && ($stmt = Database::prepare("SELECT `EmailAddress` FROM `accounts` WHERE `EmailAddress` = ? LIMIT 1")) !== false){
			$stmt->bind_param('s', $email);
			if($stmt->execute()){
				$stmt->store_result();
				if($stmt->num_rows > 0){
					Brevada::Redirect('/settings/settings?section=logins&exists=1');
				}
			}
			$stmt->close();
		}
		
		if($validity_email)
		{
			$password = bin2hex(openssl_random_pseudo_bytes(5));
			$d_password = Brevada::HashPassword($password);
			$d_companyID = $_SESSION['CompanyID'];
			
			$success = false;
			
			if($storeID > 0){
				$d_permissions = Permissions::MODIFY_STORE;
				if(($stmt = Database::prepare("INSERT INTO `accounts` (`EmailAddress`, `DateCreated`, `Password`, `CompanyID`, `StoreID`, `Permissions`) VALUES (?, NOW(), ?, ?, ?, ?)")) !== false){
					$stmt->bind_param('ssiii', $email, $d_password, $d_companyID, $storeID, $d_permissions);
					$success = $stmt->execute();
					$stmt->close();
				}
			} else {
				$d_permissions = Permissions::MODIFY_COMPANY_STORES;
				if(($stmt = Database::prepare("INSERT INTO `accounts` (`EmailAddress`, `DateCreated`, `Password`, `CompanyID`, `StoreID`, `Permissions`) VALUES (?, NOW(), ?, ?, NULL, ?)")) !== false){
					$stmt->bind_param('ssii', $email, $d_password, $d_companyID, $d_permissions);
					$success = $stmt->execute();
					$stmt->close();
				}
			}
			
			if($success !== false){
				/* Send password email. */
				
				$companyName = Database::query("SELECT `companies`.`Name` FROM `companies` WHERE `companies`.id = {$_SESSION['CompanyID']} LIMIT 1");
				if($companyName === false || $companyName->num_rows == 0){
					$companyName = '';
				} else {
					$companyName = $companyName->fetch_assoc()['Name'];
				}
				
				if(!empty($companyName)){
					$vars = array('%company_name%' => $companyName, '%password%' => $password);
				
					Email::build()->setSubject('Brevada Account Registration')->setTo($email)->loadTemplate('corporate_new_account_password.html', $vars)->send();
				}
				Brevada::Redirect('/settings/settings?section=logins');
			}
			
		}
		
		Brevada::Redirect('/settings/settings?section=logins&error=0');
	} else {
		Brevada::Redirect('/settings/settings?section=logins&max=1');
	}
} else {
	$accountID = @intval(Brevada::FromPOST('id'));
	$storeID = @intval(Brevada::FromPOST('store'));

	if(empty($accountID)){ exit('Invalid'); }

	if($storeID > 0){
		$check = Database::query("SELECT `stores`.`id` FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} AND `stores`.`id` = {$storeID} LIMIT 1");
		if($check === false || $check->num_rows == 0){
			exit('Invalid');
		}
	}

	$check = Database::query("SELECT `accounts`.`id` FROM `accounts` WHERE `accounts`.`CompanyID` = {$_SESSION['CompanyID']} AND `accounts`.`id` = {$accountID} LIMIT 1");
	if($check === false || $check->num_rows == 0){
		exit('Invalid');
	}

	if($storeID > 0){
		$permissions = Permissions::MODIFY_STORE;
		if(($query = Database::query("UPDATE `accounts` SET `accounts`.StoreID = {$storeID}, `accounts`.Permissions = {$permissions} WHERE `accounts`.id = {$accountID}")) !== false){
			exit(__(Permissions::translateH($permissions)));
		}
	} else {
		$permissions = Permissions::MODIFY_COMPANY_STORES;
		if(($query = Database::query("UPDATE `accounts` SET `accounts`.StoreID = NULL, `accounts`.Permissions = {$permissions} WHERE `accounts`.id = {$accountID}")) !== false){
			exit(__(Permissions::translateH($permissions)));
		}
	}

	exit('Invalid');
}

exit('Error');
?>