<?php
class TaskCustomers extends AbstractTask
{
	private $data;
	
	public function execute($method, $tasks, &$data)
	{
		if($method == 'get'){
			if(!TaskLoader::requiresData(['localtime'], $_GET)){
				throw new Exception("Incomplete request.");
			}
		}
		if(!Brevada::IsLoggedIn()){
			throw new Exception("Authentication required.");
		}
		$this->data = &$data;
	}
	
	public function taskList(){
		$store = @intval(Brevada::FromGET('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$rows = [];
		
		// Get groups of session_data.
		if(($stmt = Database::prepare("
			SELECT CustomerID, Acknowledged, Email, SubmissionTime FROM (
				( SELECT
					session_data.id as CustomerID,
					session_data.Acknowledged as Acknowledged,
					session_data_field.DataValueSmall as Email,
					session_data.SubmissionTime as SubmissionTime
				FROM session_data
				LEFT JOIN session_data_field ON session_data_field.SessionDataID = session_data.id
				JOIN feedback ON feedback.SessionCode = session_data.SessionCode
				JOIN aspects ON aspects.id = feedback.AspectID
				JOIN stores ON stores.id = aspects.StoreID
				JOIN companies ON companies.id = stores.CompanyID
				WHERE
					(session_data_field.DataKey = 'email' OR session_data_field.DataKey IS NULL) AND
					aspects.StoreID = ? AND
					session_data.Acknowledged = 0 AND
					`stores`.`CompanyID` = ? AND
					`companies`.`Active` = 1 AND
					`companies`.`ExpiryDate` IS NOT NULL AND
					`companies`.`ExpiryDate` > NOW()
				GROUP BY session_data.id
				ORDER BY session_data.SubmissionTime ASC
				LIMIT 10 )
				UNION
				( SELECT
					session_data.id as CustomerID,
					session_data.Acknowledged as Acknowledged,
					session_data_field.DataValueSmall as Email,
					session_data.SubmissionTime as SubmissionTime
				FROM session_data
				LEFT JOIN session_data_field ON session_data_field.SessionDataID = session_data.id
				JOIN feedback ON feedback.SessionCode = session_data.SessionCode
				JOIN aspects ON aspects.id = feedback.AspectID
				JOIN stores ON stores.id = aspects.StoreID
				JOIN companies ON companies.id = stores.CompanyID
				WHERE
					(session_data_field.DataKey = 'email' OR session_data_field.DataKey IS NULL) AND
					aspects.StoreID = ? AND
					session_data.Acknowledged = 1 AND
					`stores`.`CompanyID` = ? AND
					`companies`.`Active` = 1 AND
					`companies`.`ExpiryDate` IS NOT NULL AND
					`companies`.`ExpiryDate` > NOW()
				GROUP BY session_data.id
				ORDER BY session_data.SubmissionTime ASC
				LIMIT 10 )
			) TT ORDER BY Acknowledged ASC, SubmissionTime ASC
			")) !== false){
			$stmt->bind_param('iiii', $store, $company, $store, $company);
			if($stmt->execute()){
				$stmt->bind_result($a, $b, $c, $d);
				while($stmt->fetch()){
					$rows[] = ['id' => $a, 'Acknowledged' => $b, 'Email' => $c, 'SubmissionTime' => $d];
				}
			}
			$stmt->close();
		}
		
		$store_average = (new Data())->store($store)->getAvg();
		if ($store_average->getSize() == 0){
			$store_average = false;
		} else {
			$store_average = $store_average->getRating();
		}
		
		$customers = [];
		foreach($rows as $row){
			$lowest_rating = false;
			$lowest_title = false;
			
			$aspects = [];
			$device = 'desktop';

			if(($stmt = Database::prepare("
			SELECT feedback.Rating, aspect_type.Title, `user_agents`.`UserAgent`
				FROM feedback
				JOIN session_data ON session_data.SessionCode = feedback.SessionCode
				JOIN aspects ON aspects.id = feedback.AspectID
				JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID
				JOIN stores ON stores.id = aspects.StoreID
				JOIN companies ON companies.id = stores.CompanyID
				JOIN `user_agents` ON `user_agents`.`id` = `feedback`.`UserAgentID`
				WHERE
					aspects.StoreID = ? AND
					`stores`.`CompanyID` = ? AND
					session_data.id = ?
				ORDER BY aspect_type.Title DESC
			")) !== false){
				$stmt->bind_param('iii', $store, $company, $row['id']);
				if($stmt->execute()){
					$stmt->bind_result($percent, $title, $userAgent);
					while($stmt->fetch()){
						$aspects[] = [
							'percent' => $percent,
							'title' => $title
						];
						
						if (!$lowest_rating || $percent < $lowest_rating){
							$lowest_rating = $percent;
							$lowest_title = $title;
						}
						
						if (!empty($userAgent)){
							if(preg_match('/'.TABLET_USERAGENT.'/', $userAgent)){
								$device = 'tablet';
							} else if(preg_match('/mobile/i', $userAgent)){
								$device = 'mobile';
							}
						}
					}
				}
				$stmt->close();
			}
			
			$avg_sum = 0;
			foreach($aspects as $aspect){
				$avg_sum += intval($aspect['percent']);
			}
			$average = count($aspects) > 0 ? round(1.0*$avg_sum / count($aspects)) : false;
			
			$relative = false;
			if ($store_average !== false){
				$relative = $average - $store_average;
			}
			
			$customers[] = [
				"id" => $row['id'],
				"acknowledged" => $row['Acknowledged'],
				"date" => $row['SubmissionTime'],
				"email" => empty($row['Email']) ? false : $row['Email'],
				"aspects" => $aspects,
				"average" => $average,
				"relative" => $relative,
				"lowest" => $lowest_rating,
				"device" => $device
			];
		}
		
		$this->data['customers'] = $customers;		
	}
	
	public function taskAcknowledge()
	{
		$store = @intval(Brevada::FromPOST('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$id = empty($_POST['id']) ? 0 : trim($_POST['id']);
		
		if($id <= 0){
			throw new Exception("Invalid customer.");
		}
		
		if(($stmt = Database::prepare("
			UPDATE session_data
			JOIN (
				SELECT sd.id as sdid FROM session_data sd JOIN session_data_field ON session_data_field.SessionDataID = sd.id
				JOIN feedback ON feedback.SessionCode = sd.SessionCode
				JOIN aspects ON aspects.id = feedback.AspectID
				JOIN stores ON stores.id = aspects.StoreID
				JOIN companies ON companies.id = stores.CompanyID
				WHERE
					session_data_field.DataKey = 'email' AND
					aspects.StoreID = ? AND
					`stores`.`CompanyID` = ? AND
					sd.id = ?
				GROUP BY sd.id
				LIMIT 1
			) TT ON TT.sdid = session_data.id				
			SET session_data.Acknowledged = 1				
		")) !== false){
			$stmt->bind_param('iii', $store, $company, $id);
			$res = $stmt->execute();
			$stmt->close();
			if(!$res){
				throw new Exception("Failed to acknowledge customer.");
			}
		}
	}
}
?>