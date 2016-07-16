<?php
class TaskAspects extends AbstractTask
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
		
		$keywords = [];
		if(($stmt = Database::prepare("
			SELECT company_keywords_link.CompanyKeywordID
			FROM company_keywords_link
			WHERE
			company_keywords_link.`CompanyID` = ?")) !== false){
			$stmt->bind_param('i', $company);
			if($stmt->execute()){
				$stmt->bind_result($keywordID);
				while($stmt->fetch()){
					$keywords = @intval($keywordID);
				}
			}
			$stmt->close();
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
			`stores`.`CompanyID` = ? AND
			`companies`.`Active` = 1 AND
			`companies`.`ExpiryDate` IS NOT NULL AND
			`companies`.`ExpiryDate` > NOW()
			ORDER BY `aspect_type`.`Title`")) !== false){
			$stmt->bind_param('ii', $store, $company);
			if($stmt->execute()){
				$stmt->bind_result($a, $b, $c);
				while($stmt->fetch()){
					$rows[] = ['id' => $a, 'AspectTypeID' => $b, 'Title' => $c];
				}
			}
			$stmt->close();
		}
		
		$aspects = [];
		foreach($rows as $row){
			$aspectType = $row['AspectTypeID'];
			
			$rating = (new Data())->store($store)->aspectType($aspectType)->getAvg();
			
			$data_percent24H_A = (new Data())->store($store)->aspectType($aspectType)->from(time()-(24*3600))->getAvg();
			$data_percent24H_B = (new Data())->store($store)->aspectType($aspectType)->to(time()-(24*3600))->getAvg();
			$data_percent24H = null;
			if($data_percent24H_A->getSize() > 0){
				$data_percent24H = $data_percent24H_A->getRating() - $data_percent24H_B->getRating();
			}
				
			$data_percent4W_A = (new Data())->store($store)->aspectType($aspectType)->from(time()-(4*7*24*3600))->getAvg();
			$data_percent4W_B = (new Data())->store($store)->aspectType($aspectType)->to(time()-(4*7*24*3600))->getAvg();
			$data_percent4W = null;
			if($data_percent4W_A->getSize() > 0){
				$data_percent4W = $data_percent4W_A->getRating() - $data_percent4W_B->getRating();
			}
		
			$bucketSize = 5;
			
			$beforeBucket = (new Data())->store($store)->aspectType($aspectType)->to(time()-(2*7*24*3600))->getAvg();
			$bucket = (new Data())->store($store)->aspectType($aspectType)->from(time()-(2*7*24*3600))->getAvg($bucketSize, Data::BY_UNIFORM);
			
			$bucketDates = [];
			$bucketData = [];
			
			$minValue = 100;
			$maxValue = 0;
			
			$prev = $beforeBucket->getRating();
			for($i = 0; $i < $bucketSize; $i++){
				if(!$bucket->get($i)){ break; }
				
				$fromDate = date('M jS', $bucket->getUTCFrom($i));
				$toDate = date('M jS', $bucket->getUTCTo($i)-1);
				
				$bucketDates[] = $fromDate == $toDate ? $fromDate : $fromDate . ' - ' . $toDate;
				
				if($bucket->getSize($i) > 0){
					$prev = $bucket->getRating($i);
				}
				$bucketData[] = $prev;
				
				$minValue = min($minValue, $prev);
				$maxValue = max($maxValue, $prev);
			}
			
			$industry = (new Data())->aspectType($aspectType)->keyword($keywords)->getAvg();
			
			$aspects[] = [
				"id" => $row['id'],
				"title" => __($row['Title']),
				"size" => $rating->getSize(),
				"rating" => $rating->getRating(),
				"change" => [
					"day" => $data_percent24H,
					"month" => $data_percent4W
				],
				"bucket" => [
					"labels" => $bucketDates,
					"data" => $bucketData,
					"min" => $minValue,
					"max" => $maxValue
				],
				"industry" => $industry->getSize() == 0 ? false : $industry->getRating()
			];
		}
		
		$this->data['aspects'] = $aspects;		
	}
}
?>