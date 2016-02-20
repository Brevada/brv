<?php
class TaskFeedback extends AbstractTask
{
	public function execute($method, $tasks, &$data)
	{
		if($method == 'post'){
			if($data['secure'] !== true || !isset($data['tablet'])){
				throw new Exception("Data integrity compromised.");
			}
			
			/* Ensure all required data is present. */
			if(TaskLoader::requiresData(['serial', 'now', 'rating', 'aspectID'], $_POST)){
			
				$serial = $_POST['serial'];
				$time = $_POST['now'];
				$rating = $_POST['rating'];
				$aspectID = $_POST['aspectID'];
				$sessionID = Brevada::FromPOST('session');
				
				if(empty($sessionID)){
					$sessionID = strval(bin2hex(openssl_random_pseudo_bytes(16)));
				}
				
				/*
					Insert into Database.
				*/

				if(TaskFeedback::insertRating($rating, $aspectID, $sessionID, $time)){
					/* Good. */
					Logger::info("Rating inserted via API: {$rating}, {$aspectID}, {$sessionID}, {$time}");
				} else {
					throw new Exception("Error inserting rating.");
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

		$geo = Geography::GetGeo();

		$ipAddress = $geo['ip'];

		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		
		if(!isset($_SESSION['feedback']) || empty($_SESSION['feedback'])){
			$_SESSION['feedback'] = [];
		}
		
		if(in_array($aspectID, $_SESSION['feedback'])){
			Logger::info("Repeated feedback by same session detected @ {$time} for aspects.#{$aspectID}.");
		}
		
		if(isset($rating)){
			if(($stmt = Database::prepare("INSERT INTO `user_agents` (`UserAgent`) VALUES (?)")) !== false){
				$userAgentID = -1;
				$stmt->bind_param('s', $userAgent);
				if($stmt->execute()){
					$userAgentID = $stmt->insert_id;
				}
				$stmt->close();
				
				if($userAgentID > 0){
					if(($stmt = Database::prepare("INSERT INTO `feedback` (`AspectID`, `Date`, `Rating`, `IPAddress`, `UserAgentID`, `SessionCode`) SELECT aspects.ID, FROM_UNIXTIME(?), ?, ?, ?, ? FROM aspects WHERE aspects.ID = ?")) !== false){
						$stmt->bind_param('idsisi', $time, $rating, $ipAddress, $userAgentID, $sessionCode, $aspectID);
						if($stmt->execute()){
							Logger::info("Inserted feedback.#{$stmt->insert_id}. AspectID: {$aspectID}, Rating: {$rating}, IP: {$ipAddress}, Time: {$time}");
							
							/* Log in sessions. */
							$_SESSION['feedback'][] = $aspectID;
						}
						$stmt->close();
					}
				}
			}
		}
		return true;
	}
	
}
?>