<?php
$this->IsScript = true;
date_default_timezone_set('America/New_York');

$email = Brevada::validate(Brevada::FromPOSTGET('emailTie'), VALIDATE_DATABASE);
$userID = @intval(Brevada::FromPOSTGET('user_id'));

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

if(($check = Database::prepare("SELECT users.id FROM users WHERE users.id = ? LIMIT 1")) !== false){
	$check->bind_param('i', $userID);
	if($check->execute()){
		$check->store_result();
		if($check->num_rows > 0){
			$check->close();
			$userExists = true;
		}
	}
}

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
					
					if($userAgentID > 0){
						if(($stmt = Database::prepare("INSERT INTO `subscriptions` (`UserID`, `Date`, `EmailAddress`, `IPAddress`, `UserAgentID`, `Country`, `Province`, `City`, `UniqueCode`) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)")) !== false){
							$stmt->bind_param('ississss', $userID, $email, $ipAddress, $userAgentID, $country, $province, $city, $unique);
							$stmt->execute();
							$stmt->close();
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