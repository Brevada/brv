<?php
$dest = '/home/signup.php?error';

$email = Brevada::FromPOST('txtEmail');
$password = trim(Brevada::FromPOST('txtPassword'));
$password2 = trim(Brevada::FromPOST('txtPassword2'));
$name = trim(Brevada::FromPOST('txtCompanyName'));

$plan = @intval(Brevada::validate(Brevada::FromPOST('plan')));
$plan = $plan < 0 || $plan > 2 ? 0 : $plan;

$aspects = Brevada::FromPOST('tokensAspects');
$keywords = Brevada::FromPOST('tokensKeywords');

$website = trim(Brevada::FromPOST('txtWebsite'));

$stores = array();

$isCorporate = Brevada::FromGET('corporate') == '1' && Brevada::IsLoggedIn() && Permissions::has(Permissions::EDIT_ADMIN);

while(isset($_POST['txtStreetAddress'.count($stores)])){
	$index = count($stores);
	$stores[] = array('Name' => strtolower(trim(Brevada::FromPOST('txtStoreName'.$index))), 'StreetAddress' => strtolower(trim(Brevada::FromPOST('txtStreetAddress'.$index))), 'City' => strtolower(trim(Brevada::FromPOST('txtCity'.$index))), 'Province' => strtolower(trim(Brevada::FromPOST('txtProvince'.$index))), );
}

if(empty($email) || empty($password) || empty($name)){
	$dest = '/home/signup.php?invalid';
} else {

	$validity_email = true;
	$validity_name = false;
	$validity_address = false;

	/* Check validity of password. */
	$validity_password = $password == $password2;
	
	if($validity_password)
	{
		/* Check validity of email. */
		$validity_email = filter_var($email, FILTER_VALIDATE_EMAIL);
		
		if($validity_email && ($stmt = Database::prepare("SELECT `EmailAddress` FROM `accounts` WHERE `EmailAddress` = ? LIMIT 1")) !== false){
			$stmt->bind_param('s', $email);
			if($stmt->execute()){
				$stmt->store_result();
				if($stmt->num_rows > 0){
					$validity_email = false;
					$dest = '/home/signup.php?emailexists';
				}
			}
			$stmt->close();
		}
		
		if($validity_email)
		{
			/* Check validity of name. */			
			$reserved_names = array('index', '404', 'approved', 'complete', 'corporate', 'dashboard', 'home', 'ipn', 'ipnlistener', 'login', 'logout', 'payment', 'pricing', 'signup', 'tablet', 'thanks', 'upgrade', 'voting', 'about', 'account', 'secure', 'images', 'user_data', 'overall');
			
			$validity_name = !empty(strtolower(preg_replace("/[^a-zA-Z\-]+/", "", $name)));
			
			if($validity_name)
			{
				/* Check validity of address. */
				
				$d_maxTablets = 0;				
				$d_maxAccounts = 1;
				$d_maxStores = 1;
				
				if($plan == 1){
					$d_maxTablets = 2;				
					$d_maxAccounts = 1;
				} else if($plan == 2){
					$d_maxTablets = 5;				
					$d_maxAccounts = 3;
				}
				
				$d_companyName = $name;
				
				$d_categoryID = -1;
				$d_featuresID = -1;
				
				$d_companyID = -1;
				
				$numStores = 0;
				
				/* Get category ID. */
				$d_categoryID = @intval(Brevada::FromPOST('ddCategory'));
				
				$query = Database::query("SELECT `id` FROM `company_categories` WHERE `company_categories`.`id` = {$d_categoryID} LIMIT 1");
				if($query->num_rows == 0){
					$d_categoryID = 1;
				} else {
					$row = $query->fetch_assoc();
					$d_categoryID = $row['id'];
				}
				
				foreach($stores as $store){
					if($numStores == 1 && !$isCorporate){ break; }
				
					$storename = $store['Name'];
					$streetaddress = $store['StreetAddress'];
					$city = $store['City'];
					$province = $store['Province'];
					
					if(empty($storename)){ $storename = $name . " - #".($numStores+1) }
					
					if(empty($streetaddress) || empty($city) || empty($province)){ continue; }
					
					/*
						TODO: Use Google Geocoding API and Google Places API
						to confirm validity of address and retrieve longitude
						and latitude.
					*/
					$validity_address = !empty($streetaddress) && !empty($city) && !empty($province);
				
					if($validity_address)
					{
						/* Prepare all data for database insertions. */
						
						if($numStores == 0){
							/* Insert features. */
							if(($stmt = Database::prepare("INSERT INTO `company_features` (`MaxTablets`, `MaxAccounts`, `MaxStores`) VALUES (?, ?, ?)")) !== false){
								$stmt->bind_param('iii', $d_maxTablets, $d_maxAccounts, $d_maxStores);
								if($stmt->execute()){
									$d_featuresID = $stmt->insert_id;
								}
								$stmt->close();
							}				
							
							/* Insert company. */
							if(($stmt = Database::prepare("INSERT INTO `companies` (`Name`, `CategoryID`, `FeaturesID`, `Website`) VALUES (?, ?, ?, ?)")) !== false){
								$stmt->bind_param('siis', $d_companyName, $d_categoryID, $d_featuresID, $website);
								if($stmt->execute()){
									$d_companyID = $stmt->insert_id;
								}
								$stmt->close();
							}
							
							/* Link company keywords. */
							$keywords = explode(',', $keywords);
							$keywords = array_unique($keywords, SORT_NUMERIC);
							if($keywords !== false){
								foreach($keywords as $keyword){
									if(!empty($keyword)){
										if(($stmt = Database::prepare("INSERT INTO `company_keywords_link` (`CompanyKeywordID`, `CompanyID`) VALUES (?, ?)")) !== false){
											$stmt->bind_param('ii', $keyword, $d_companyID);
											$stmt->execute();
											$stmt->close();
										}
									}
								}
							}
						}
						
						$d_locationID = -1;
						$d_dashboardSettingsID = -1;
						
						$d_storeID = -1;
						
						$d_password = Brevada::HashPassword($password);
						
						$d_permissions = Permissions::MODIFY_COMPANY;
						
						$d_storeName = $storename;
						
						$d_urlName = $url_name_root = strtolower(preg_replace("/[^a-zA-Z\-]+/", "", $storename));
						
						$url_name_mod = 1;
						
						while(Database::query("SELECT `URLName` FROM `stores` WHERE URLName='{$d_urlName}'")->num_rows > 0 || in_array($d_urlName, $reserved_names)) {
							$d_urlName = Brevada::validate($url_name_root . strval($url_name_mod++), VALIDATE_DATABASE);
						}
						
						/* Insert location. */
						if(($stmt = Database::prepare("INSERT INTO `locations` (`Country`, `Province`, `City`) VALUES (?, ?, ?)")) !== false){
							$stmt->bind_param('sss', $streetaddress, $city, $province);
							if($stmt->execute()){
								$d_locationID = $stmt->insert_id;
							}
							$stmt->close();
						}
						
						/* Insert store. */
						if(($stmt = Database::prepare("INSERT INTO `stores` (`Name`, `CompanyID`, `Active`, `URLName`, `LocationID`) VALUES (?, ?, 1, ?, ?)")) !== false){
							$stmt->bind_param('sisi', $d_storeName, $d_companyID, $d_urlName, $d_locationID);
							if($stmt->execute()){
								$d_storeID = $stmt->insert_id;
								$numStores++;
							}
							$stmt->close();
						}
						
						/* Insert dashboard. */
						if(Database::query("INSERT INTO dashboard_settings () VALUES ()")){
							$d_dashboardSettingsID = Database::getCon()->insert_id;
							Database::query("INSERT INTO dashboard (`StoreID`, `SettingsID`) VALUES ({$d_storeID}, {$d_dashboardSettingsID})");
						}
						
						/* Insert aspects. */
						$aspects = explode(',', $aspects);
						if($aspects !== false){
							foreach($aspects as $token){
								if(!empty($token)){
									if(($stmt = Database::prepare("INSERT INTO aspects (`StoreID`, `AspectTypeID`) SELECT stores.id, (SELECT aspect_type.ID FROM aspect_type WHERE aspect_type.ID = ?) as AspectTypeID FROM stores WHERE stores.id = ?")) !== false){
										$stmt->bind_param('ii', $token, $d_storeID);
										$stmt->execute();
										$stmt->close();
									}
								}
							}
						}
						
						$dest = '/dashboard';
					}
				
				}
				
				if($numStores > 1 && $isCorporate){
					/* Insert admin account. */
					if(($stmt = Database::prepare("INSERT INTO `accounts` (`EmailAddress`, `Password`, `CompanyID`, `StoreID`, `Permissions`) VALUES (?, ?, ?, NULL, ?)")) !== false){
						$stmt->bind_param('ssii', $email, $d_password, $d_companyID, $d_permissions);
						$stmt->execute();
						$stmt->close();
					}
				} else {				
					/* Insert admin account. */
					if(($stmt = Database::prepare("INSERT INTO `accounts` (`EmailAddress`, `Password`, `CompanyID`, `StoreID`, `Permissions`) VALUES (?, ?, ?, ?, ?)")) !== false){
						$stmt->bind_param('ssiii', $email, $d_password, $d_companyID, $d_storeID, $d_permissions);
						$stmt->execute();
						$stmt->close();
					}
				}
				
				if(Brevada::IsLoggedIn() && Permissions::has(Permissions::EDIT_ADMIN)){
					$dest = '/admin?show=newclient&done=1';
				} else {
					Brevada::Login($email, $password);
				}
				
			}
		}
	}
	
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>