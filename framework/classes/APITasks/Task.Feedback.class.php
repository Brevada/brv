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
			if(TaskLoader::requiresData(['serial', 'now'], $_POST)){
				$serial = $_POST['serial'];
				$time = $_POST['now'];
				
				$sessionID = Brevada::FromPOST('session');
				if(empty($sessionID)){
					$sessionID = strval(bin2hex(openssl_random_pseudo_bytes(16)));
				}
				
				if(isset($_POST['rating']) && isset($_POST['aspectID'])){
					$rating = $_POST['rating'];
					$aspectID = $_POST['aspectID'];

					
					/*
						Insert into Database.
					*/

					if(TaskFeedback::insertRating($rating, $aspectID, $sessionID, $time, $serial)){
						/* Good. */
						Logger::info("Rating inserted via API: {$rating}, {$aspectID}, {$sessionID}, {$serial}, {$time}");
					} else {
						throw new Exception("Error inserting rating.");
					}
				}
				
				$fields = false;
				if(isset($_POST['fields'])){
					$fields = json_decode($_POST['fields'], true);
				}

				TaskFeedback::insertSessionData($sessionID, $fields, $time);
				
			} else {
				throw new Exception("Incomplete request.");
			}
			
		}
	}
	
	public static function insertSessionData($sessionCode, $fields, $time)
	{
		$sessionDataID = -1;

		if(($stmt = Database::prepare("
			INSERT IGNORE INTO `session_data` (`SessionCode`, `SubmissionTime`) VALUES (?, ?)
		")) !== false){
			$stmt->bind_param('si', $sessionCode, $time);
			if($stmt->execute()){
				$sessionDataID = $stmt->insert_id;
			}
			$stmt->close();
		}

		if($sessionDataID == 0){
			// Session already exists.
			if(($stmt = Database::prepare("
				SELECT id, SubmissionTime FROM `session_data`
				WHERE SessionCode = ?
				LIMIT 1
			")) !== false){
				$stmt->bind_param('i', $sessionCode);
				if($stmt->execute()){
					$stmt->bind_result($sessionDataID, $sessionTime);
					if($stmt->fetch()){
						if ($time - intval($sessionTime) > 24*60*60){
							Logger::info("Inserted into session_data#{$sessionDataID} 24 hours after row created. Time of attempt: {$time}. Time of creation: {$sessionTime}.");
						}
					}
				}
				$stmt->close();
			}
		}
		
		if($sessionDataID > 0 && $fields){
			foreach($fields as $key => $data){
				if(empty($key)){ continue; }
				
				$label = isset($data['label']) ? $data['label'] : '';
				$value = isset($data['value']) ? $data['value'] : '';
				
				$sql = "
					INSERT INTO `session_data_field` 
					(`SessionDataID`, `DataLabel`, `DataKey`, `DataValueSmall`, `DataValueLarge`) 
					VALUES (?, ?, ?, ?, NULL)
				";
				if(strlen($value) >= 255){
					$sql = "INSERT INTO `session_data_field` 
					(`SessionDataID`, `DataLabel`, `DataKey`, `DataValueLarge`, `DataValueSmall`) 
					VALUES (?, ?, ?, ?, NULL)";
				}
				
				if(($stmt = Database::prepare($sql)) !== false){
					$stmt->bind_param('ssss', $sessionDataID, $label, $key, $value);
					if($stmt->execute()){
						Logger::info("Inserted session_data_field#{$stmt->insert_id}. Time: {$time}.");
					}
					$stmt->close();
				}
			}
		}
	}
	
	public static function insertRating($rating, $aspectID, $sessionCode, $time = 0, $serial = null)
	{
		if($time == 0){ $time = time(); }
		$time = @intval($time);

		$ipAddress = Geography::GetIP();

		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		
		if(!isset($_SESSION['feedback']) || empty($_SESSION['feedback'])){
			$_SESSION['feedback'] = [];
		}
		
		if(in_array($aspectID, $_SESSION['feedback'])){
			Logger::info("Repeated feedback by same session detected @ {$time} for aspects.#{$aspectID}.");
		}
		
		if(isset($rating)){
			if(($stmt = Database::prepare("
				INSERT INTO `user_agents` (`UserAgent`, `TabletID`)
				SELECT `UserAgent`, `TabletID` FROM ((SELECT ? as `UserAgent`, `tablets`.id as `TabletID` FROM `tablets`
				WHERE `tablets`.`SerialCode` = ? LIMIT 1) UNION (SELECT ? as `UserAgent`, NULL as `TabletID`)) T LIMIT 1
			")) !== false){
				$userAgentID = -1;
				$stmt->bind_param('sss', $userAgent, $serial, $userAgent);
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