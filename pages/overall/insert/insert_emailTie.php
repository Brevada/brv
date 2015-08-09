<?php
$this->IsScript = true;
date_default_timezone_set('America/New_York');

$email = Brevada::validate(Brevada::FromPOSTGET('emailTie'), VALIDATE_DATABASE);
$storeID = @intval(Brevada::FromPOSTGET('store_id'));

if(!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){ exit('Invalid'); }

$geo = Geography::GetGeo();

$ipAddress = $geo['ip'];
$country = $geo['country'];
$province = $geo['province'];
$city = $geo['city'];

$userAgent = $_SERVER['HTTP_USER_AGENT'];

/* Authorized tablet user agent. */
$authUserAgent = TABLET_USERAGENT;

$userExists = false;

if(($check = Database::prepare("SELECT stores.id FROM stores WHERE stores.id = ? LIMIT 1")) !== false){
	$check->bind_param('i', $storeID);
	if($check->execute()){
		$check->store_result();
		if($check->num_rows > 0){
			$check->close();
			$userExists = true;
		}
	}
}

$sessionCode = isset($_SESSION['SessionCode']) ? $_SESSION['SessionCode'] : '';

if($userExists && ($check = Database::prepare("SELECT `subscriptions`.id FROM `subscriptions` WHERE `subscriptions`.EmailAddress = ? LIMIT 1")) !== false){
	$check->bind_param('s', $email);
	if($check->execute()){
		$check->store_result();
		if($check->num_rows == 0){
			if(isset($email)){
				if(($stmt = Database::prepare("INSERT INTO `user_agents` (`UserAgent`) VALUES (?)")) !== false){
					$userAgentID = -1;
					$stmt->bind_param('s', $userAgent);
					if($stmt->execute()){
						$userAgentID = $stmt->insert_id;
					}
					$stmt->close();
					
					/* Unique public identifier (used for unsubscribing) */
					$unique = strval(bin2hex(openssl_random_pseudo_bytes(10)));
					
					if(($stmt = Database::prepare("INSERT INTO `locations` (`Country`, `Province`, `City`) VALUES (?, ?, ?)")) !== false){
						$locationID = -1;
						$stmt->bind_param('sss', $country, $province, $city);
						if($stmt->execute()){
							$locationID = $stmt->insert_id;
						}
						$stmt->close();
						
						if($userAgentID > 0 && $locationID > 0){
							if(($stmt = Database::prepare("INSERT INTO `subscriptions` (`StoreID`, `Date`, `EmailAddress`, `IPAddress`, `UserAgentID`, `LocationID`, `UniqueCode`, `SessionCode`) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)")) !== false){
								$stmt->bind_param('issiiss', $storeID, $email, $ipAddress, $userAgentID, $locationID, $unique, $sessionCode);
								$stmt->execute();
								$stmt->close();
								
								if(isset($_SESSION['SessionCode'])){
									unset($_SESSION['SessionCode']);
								}
							}
						}
					}
				}
			}
		} else { exit('Invalid'); }
	}
	$check->close();
} else { exit('Invalid'); }

exit('OK');
?>