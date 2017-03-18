<?php
class TaskMilestones extends AbstractTask
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
	
	public function taskCreate()
	{
		$store = @intval(Brevada::FromPOST('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$title = empty($_POST['title']) ? '' : trim($_POST['title']);
		$from = empty($_POST['from']) ? 0 : @intval($_POST['from']);
		$to = empty($_POST['to']) ? 0 : @intval($_POST['to']);
		
		if(empty($title)){
			throw new Exception("Missing field. Title required.");
		}
		
		if($from <= 0 || ($to > 0 && $to < $from)){
			throw new Exception("Invalid dates.");
		}
		
		if($to == 0){
			// Open ended milestone.
			if(($stmt = Database::prepare("INSERT INTO milestones (`StoreID`, `Title`, `FromDate`, `ToDate`) VALUES (?, ?, FROM_UNIXTIME(?), NULL)")) !== false){
				$stmt->bind_param('isi', $store, $title, $from);
				$res = $stmt->execute();
				$stmt->close();
				if(!$res){
					throw new Exception("Failed to create event.");
				}
			}
		} else {
			// Definite end date.
			if(($stmt = Database::prepare("INSERT INTO milestones (`StoreID`, `Title`, `FromDate`, `ToDate`) VALUES (?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?))")) !== false){
				$stmt->bind_param('isii', $store, $title, $from, $to);
				$res = $stmt->execute();
				$stmt->close();
				if(!$res){
					throw new Exception("Failed to create event.");
				}
			}
		}
	}
	
	public function taskDelete()
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
			throw new Exception("Invalid event.");
		}
		
		if(($stmt = Database::prepare("DELETE FROM milestones WHERE StoreID = ? AND id = ? LIMIT 1")) !== false){
			$stmt->bind_param('ii', $store, $id);
			$res = $stmt->execute();
			$stmt->close();
			if(!$res){
				throw new Exception("Failed to delete event.");
			}
		}
	}
	
	public function taskComplete()
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
			throw new Exception("Invalid event.");
		}
		
		if(($stmt = Database::prepare("UPDATE milestones SET ToDate = NOW() WHERE StoreID = ? AND id = ? AND ToDate IS NULL LIMIT 1")) !== false){
			$stmt->bind_param('ii', $store, $id);
			$res = $stmt->execute();
			$stmt->close();
			if(!$res){
				throw new Exception("Failed to complete event.");
			}
		}
	}
	
	public function taskAspect()
	{
		$store = @intval(Brevada::FromPOST('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$milestone_id = empty($_POST['id']) ? 0 : trim($_POST['id']);
		$aspect_id = empty($_POST['aid']) ? 0 : trim($_POST['aid']);
		
		if($milestone_id <= 0 || $aspect_id <= 0){
			throw new Exception("Invalid event or aspect.");
		}
		
		$delete = !empty($_POST['delete']) && $_POST['delete'] == true;
		
		if($delete){
			if(($stmt = Database::prepare("DELETE FROM milestone_aspects WHERE milestone_aspects.id = ? AND EXISTS (SELECT 1 FROM milestones JOIN aspects ON aspects.StoreID = milestones.StoreID WHERE milestones.id = milestone_aspects.MilestoneID AND aspects.StoreID = ?)")) !== false){
				$stmt->bind_param('ii', $aspect_id, $store);
				$res = $stmt->execute();
				$stmt->close();
				if(!$res){
					throw new Exception("Failed to remove aspect from event.");
				}
			}
		} else {
			if(($stmt = Database::prepare("INSERT INTO milestone_aspects (`MilestoneID`, `AspectID`) SELECT milestones.id, aspects.id FROM milestones JOIN aspects ON aspects.StoreID = milestones.StoreID WHERE aspects.StoreID = ? AND aspects.id = ? AND milestones.id = ?")) !== false){
				$stmt->bind_param('iii', $store, $aspect_id, $milestone_id);
				$res = $stmt->execute();
				$stmt->close();
				if(!$res){
					throw new Exception("Failed to add aspect to event.");
				}
			}
		}
	}
	
	public function taskList()
	{
		$store = @intval(Brevada::FromGET('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$milestones = [];
		
		if(($stmt = Database::prepare("
			SELECT milestones.id, milestones.Title, UNIX_TIMESTAMP(milestones.FromDate) as FromDate, IF(milestones.ToDate IS NULL, 0, UNIX_TIMESTAMP(milestones.ToDate)) as ToDate FROM milestones WHERE milestones.StoreID = ? ORDER BY milestones.FromDate ASC, milestones.id")) !== false){
			$stmt->bind_param('i', $store);
			if($stmt->execute()){
				$stmt->bind_result($milestone_id, $milestone_title, $milestone_from, $milestone_to);
				while($stmt->fetch()){
					$milestones[] = [
						'id' => $milestone_id,
						'title' => $milestone_title,
						'date' => [
							'start' => $milestone_from,
							'end' => $milestone_to
						],
						'aspects' => []
					];
				}
			}
			$stmt->close();
		}
		
		foreach($milestones as &$milestone){
			if(($stmt = Database::prepare("
				SELECT milestone_aspects.id, aspect_type.id as AspectType, aspect_type.Title FROM milestone_aspects JOIN aspects ON aspects.id = milestone_aspects.AspectID JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID WHERE milestone_aspects.MilestoneID = ? ORDER BY aspect_type.Title")) !== false){
				$stmt->bind_param('i', $milestone['id']);
				if($stmt->execute()){
					$stmt->bind_result($aspect_id, $aspect_type, $aspect_title);
					while($stmt->fetch()){
						$milestone['aspects'][] = [
							'id' => $aspect_id,
							'type' => $aspect_type,
							'title' => $aspect_title,
							'change' => '',
							'responses' => 0
						];
					}
				}
				$stmt->close();
			}
		} unset($milestone);
		
		foreach($milestones as &$milestone){
			$overall_sum = 0;
			foreach($milestone['aspects'] as &$aspect){
				$from = intval($milestone['date']['start']);
				$to = intval($milestone['date']['end']);
				$aspect_type = intval($aspect['type']);
				
				$before = (new Data())->store($store)->aspectType($aspect_type)->to($from)->getAvg();
				$after = (new Data())->store($store)->aspectType($aspect_type)->to($to > 0 ? $to : time())->getAvg();
				
				$aspect['change'] = strval(round($after->getRating() - $before->getRating()));
				$aspect['responses'] = $after->getSize() - $before->getSize();
				
				$overall_sum += intval($aspect['change']);
			}
			$milestone['mood'] = @intval(floatval($overall_sum) / floatval(count($milestone['aspects'])));
		}
		
		$aspects = [];
		if(($stmt = Database::prepare("
			SELECT aspect_type.Title, aspects.id FROM aspects JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID WHERE aspects.StoreID = ? ORDER BY aspect_type.Title")) !== false){
			$stmt->bind_param('i', $store);
			if($stmt->execute()){
				$stmt->bind_result($title, $id);
				while($stmt->fetch()){
					$aspects[] = ['title' => $title, 'id' => $id];
				}
			}
			$stmt->close();
		}
		
		$this->data['milestones'] = $milestones;
		$this->data['aspects'] = $aspects;
	}
}
?>