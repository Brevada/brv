<?php
$this->IsScript = true;
date_default_timezone_set('America/New_York');

/* TODO: Test insert_rating.php */

$rating = Brevada::validate(Brevada::FromPOSTGET('value'), VALIDATE_DATABASE);
$aspectID = @intval(Brevada::FromPOSTGET('post_id'));

/*
	Removed reviewers. - Not a good way to test for repeat users.
*/

$geo = Geography::GetGeo();

$ipAddress = $geo['ip'];
$country = $geo['country'];
$province = $geo['province'];
$city = $geo['city'];

$userAgent = $_SERVER['HTTP_USER_AGENT'];

/* Authorized tablet user agent. */
$authUserAgent = TABLET_USERAGENT;

if(($check = Database::prepare("SELECT `feedback`.id FROM `feedback` LEFT JOIN user_agents ON user_agents.ID = feedback.UserAgentID WHERE `feedback`.AspectID = ? AND `feedback`.IPAddress = ? AND (`feedback`.`Date` > NOW() - INTERVAL 1 HOUR) AND `user_agents`.UserAgent = ? AND `user_agents`.UserAgent <> ? LIMIT 1")) !== false){
	$check->bind_param('isss', $aspectID, $ipAddress, $userAgent, $authUserAgent);
	if($check->execute()){
		$check->store_result();
		if($check->num_rows == 0){
			if(isset($rating)){
				if(($stmt = Database::prepare("INSERT INTO `user_agents` (`UserAgent`) VALUES (?)")) !== false){
					$userAgentID = -1;
					$stmt->bind_param('s', $userAgent);
					if($stmt->execute()){
						$userAgentID = $stmt->insert_id;
					}
					$stmt->close();
					
					if($userAgentID > 0){
					
						if(($stmt = Database::prepare("INSERT INTO `feedback` (`AspectID`, `Date`, `Rating`, `IPAddress`, `UserAgentID`, `Country`, `Province`, `City`) SELECT aspects.ID, NOW(), ?, ?, ?, ?, ?, ? FROM aspects WHERE aspects.ID = ?")) !== false){
							$stmt->bind_param('dsisssi', $rating, $ipAddress, $userAgentID, $country, $province, $city, $aspectID);
							$stmt->execute();
							$stmt->close();
						}
					}
				}
			}
		}
	}
	$check->close();
}

exit('OK');
?>