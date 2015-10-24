<?php
class TaskFeedback extends AbstractTask
{
	public function execute($method, $tasks, &$data)
	{
		if($method == 'post'){
			$k = $_POST['k'];
			
			/* Ensure all required data is present. */
			if(TaskLoader::requiresData(['serial', 'now', 'rating', 'aspectID', 'batteryLevel', 'batteryIsPlugged'], $_POST)){
			
				$serial = $_POST['serial'];
				$time = $_POST['now'];
				$rating = $_POST['rating'];
				$aspectID = $_POST['aspectID'];
				$bLevel = $_POST['batteryLevel'];
				$bPlugged = $_POST['batteryIsPlugged'];
				
				/* Check data integrity. */
				$computedHash = strtoupper(sha1(
					$serial . $time . $rating . $aspectID . $bLevel . $bPlugged
				));
				
				if($computedHash === $_POST['hash']){
					
					/*
						Insert into Database.
						Use $computedHash has session, although it is meaningless in this case.
					*/
					if(TaskFeedback::insertRating($rating, $aspectID, $computedHash, $time)){
						/* Good. */
					} else {
						throw new Exception("Error inserting rating.");
					}
					
				} else {
					throw new Exception("Data integrity compromised.");
				}
				
			} else {
				throw new Exception("Incomplete request.");
			}
			
		}
	}
	
	public static function insertRating($rating, $aspectID, $sessionCode, $time = 0)
	{
		if($time == 0){ $time = time(); }
		$time = @intval($time);
		
		/* Authorized tablet user agent. */
		$authUserAgent = TABLET_USERAGENT.'%';

		$geo = Geography::GetGeo();

		$ipAddress = $geo['ip'];
		$country = $geo['country'];
		$province = $geo['province'];
		$city = $geo['city'];

		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		
		if(($check = Database::prepare("SELECT `feedback`.id FROM `feedback` LEFT JOIN user_agents ON user_agents.ID = feedback.UserAgentID WHERE `feedback`.AspectID = ? AND `feedback`.IPAddress = ? AND (`feedback`.`Date` > NOW() - INTERVAL 1 HOUR) AND `user_agents`.UserAgent = ? AND `user_agents`.UserAgent NOT LIKE ? LIMIT 1")) !== false){
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
							
							if(($stmt = Database::prepare("INSERT INTO `locations` (`Country`, `Province`, `City`) VALUES (?, ?, ?)")) !== false){
								$locationID = -1;
								$stmt->bind_param('sss', $country, $province, $city);
								if($stmt->execute()){
									$locationID = $stmt->insert_id;
								}
								$stmt->close();
								
								if($userAgentID > 0 && $locationID > 0){
									if(($stmt = Database::prepare("INSERT INTO `feedback` (`AspectID`, `Date`, `Rating`, `IPAddress`, `UserAgentID`, `LocationID`, `SessionCode`) SELECT aspects.ID, FROM_UNIXTIME(?), ?, ?, ?, ?, ? FROM aspects WHERE aspects.ID = ?")) !== false){
										$stmt->bind_param('idsiisi', $time, $rating, $ipAddress, $userAgentID, $locationID, $sessionCode, $aspectID);
										if($stmt->execute()){
											Logger::info("Inserted feedback.#{$stmt->insert_id}. AspectID: {$aspectID}, Rating: {$rating}, IP: {$ipAddress}, Time: {$time}");
										}
										$stmt->close();
									}
								}
							}
						}
					}
				}
			} else {
				return false;
			}
			$check->close();
		} else { return false; }
		return true;
	}
	
}
?>