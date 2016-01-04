<?php
class TaskBDFF extends AbstractTask
{
	private $data;
	
	public function execute($method, $tasks, &$data)
	{
		if($method == 'get'){
			if(!TaskLoader::requiresData(['localtime'], $_GET)){
				throw new Exception("Incomplete request.");
			}
			if(!Brevada::IsLoggedIn()){
				throw new Exception("Authentication required.");
			}
			$this->data = &$data;
		}
	}
	
	public function taskHoverpod(){
		$store = @intval(Brevada::FromGET('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$tablets = 0;
		if(($stmt = Database::prepare("
			SELECT COUNT(*) as cnt FROM tablets WHERE StoreID = ?")) !== false){
			$stmt->bind_param('i', $store);
			if($stmt->execute()){
				$stmt->bind_result($tablets);
				$stmt->fetch();
			}
			$stmt->close();
		}
		
		$this->data['hoverpod'] = [
			'tablets' => $tablets,
			'responses' => (new Data())->store($store)->from(time()-3600)->getAvg()->getSize()
		];
	}
	
	public function taskAspects(){
		$store = @intval(Brevada::FromGET('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$keyword_rows = [];
		if(($stmt = Database::prepare("
			SELECT company_keywords_link.CompanyKeywordID
			FROM company_keywords_link
			WHERE
			company_keywords_link.`CompanyID` = ?")) !== false){
			$stmt->bind_param('i', $company);
			if($stmt->execute()){
				$result = $stmt->get_result();
				$keyword_rows = $result->fetch_all(MYSQLI_ASSOC);
			}
			$stmt->close();
		}
		
		$keywords = [];
		foreach($keyword_rows as $row){
			if(!empty($row['CompanyKeywordID'])){
				$keywords[] = @intval($row['CompanyKeywordID']);
			}
		}
		
		$rows = [];
		
		if(($stmt = Database::prepare("
			SELECT `aspects`.`id`, `aspect_type`.`id` as `AspectTypeID`,
			`aspect_type`.`Title`
			FROM `aspects`
			JOIN `aspect_type` ON `aspect_type`.`id` = `aspects`.`AspectTypeID`
			JOIN `stores` ON `stores`.`id` = `aspects`.`StoreID`
			JOIN companies ON companies.`id` = stores.`CompanyID`
			WHERE
			`aspects`.`StoreID` = ? AND
			`aspects`.`Active` = 1 AND
			`stores`.`CompanyID` = ?
			ORDER BY `aspect_type`.`Title`")) !== false){
			$stmt->bind_param('ii', $store, $company);
			if($stmt->execute()){
				$result = $stmt->get_result();
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
			$stmt->close();
		}
		
		$aspects = [];
		foreach($rows as $row){
			$aspectType = $row['AspectTypeID'];
			
			$rating = (new Data())->store($store)->aspectType($aspectType)->getAvg();
			
			$data_percent24H = DataResult::diffRating(
				(new Data())->store($store)->aspectType($aspectType)->from(time()-(24*3600))->getAvg(),
				(new Data())->store($store)->aspectType($aspectType)->to(time()-(24*3600))->getAvg()
			);
				
			$data_percent4W = DataResult::diffRating(
				(new Data())->store($store)->aspectType($aspectType)->from(time()-(4*7*24*3600))->getAvg(),
				(new Data())->store($store)->aspectType($aspectType)->to(time()-(4*7*24*3600))->getAvg()
			);
			
			$bucketSize = 5;
				
			$bucket = (new Data())->store($store)->aspectType($aspectType)->from(time()-(2*7*24*3600))->getAvg($bucketSize, Data::BY_UNIFORM);
			
			$bucketDates = [];
			$bucketData = [];
			
			for($i = 0; $i < $bucketSize; $i++){
				if(!$bucket->get($i)){ break; }
				$bucketDates[] = date('M jS', $bucket->getUTC($i));
				$bucketData[] = $bucket->getRating($i);
			}
			
			$aspects[] = [
				"id" => $row['id'],
				"title" => $row['Title'],
				"size" => $rating->getSize(),
				"rating" => $rating->getRating(),
				"change" => [
					"day" => $data_percent24H,
					"month" => $data_percent4W
				],
				"bucket" => [
					"labels" => $bucketDates,
					"data" => $bucketData
				],
				"industry" => (new Data())->store($store)->aspectType($aspectType)->keyword($keywords)->getAvg()->getRating()
			];
		}
		
		$this->data['aspects'] = $aspects;		
	}
}
?>