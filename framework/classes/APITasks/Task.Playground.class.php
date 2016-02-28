<?php
class TaskPlayground extends AbstractTask
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
	
	public function taskAll(){
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
		
		$HOUR = 3600; $DAY = $HOUR * 24; $WEEK = $DAY * 7; $MONTH = 52*$WEEK / 12;
		
		$minDate = time() - $MONTH;
		
		$from = max(@intval(Brevada::FromGET('from')), 0);
		$to = @intval(Brevada::FromGET('to'));
		if($to == 0){ $to = time(); }
		$excluded = empty($_GET['excluded']) ? [] : explode(',', $_GET['excluded']);
		
		$dateFormat = 'g:i:s a';
		$delta = $to - $from;
		if($delta >= $MONTH*12 - $DAY){
			if(date('Y', $from) != date('Y', $to)){
				$dateFormat = 'M, Y';
			} else {
				$dateFormat = 'M';
			}
		} else if($delta > $DAY){
			$dateFormat = 'M jS';
		} else if($delta > 60*30){
			$dateFormat = 'g:i a';
		}
		
		$bucketSize = 12;
		
		$aspects = [];
		foreach($rows as $row){			
			$aspectType = $row['AspectTypeID'];
			
			if(in_array($row['id'], $excluded)){
				$aspects[] = [
					"id" => $row['id'],
					"title" => __($row['Title'])
				];
				continue;
			}
			
			$overall = (new Data())->store($store)->aspectType($aspectType)->getAvg();
			$minDate = min($overall->getUTCFrom(), $minDate);
			
			$rating = (new Data())->store($store)->aspectType($aspectType)->from($from)->to($to)->getAvg();
			
			$bucket = (new Data())->store($store)->aspectType($aspectType)->from($from)->to($to)->getAvg($bucketSize, Data::BY_UNIFORM);
			
			$bucketDates = [];
			$bucketData = [];
			
			for($i = 0; $i < $bucketSize; $i++){
				if(!$bucket->get($i)){ break; }
				if(date($dateFormat, $bucket->getUTCFrom($i)) == date($dateFormat, $bucket->getUTCTo($i)-1)){
					$bucketDates[] = date($dateFormat, $bucket->getUTCFrom($i));
				} else {
					$bucketDates[] = date($dateFormat, $bucket->getUTCFrom($i)) . ' - ' . date($dateFormat, $bucket->getUTCTo($i)-1);
				}
				$bucketData[] = $bucket->getRating($i);
			}
			
			$aspects[] = [
				"id" => $row['id'],
				"title" => __($row['Title']),
				"all" => [
					"size" => $overall->getSize(),
					"percent" => $overall->getRating()
				],
				"bucket" => [
					"labels" => $bucketDates,
					"data" => $bucketData,
					"size" => $rating->getSize(),
					"average" => $rating->getRating()
				]
			];
		}
		
		$bucket = (new Data())->store($store)->from($from)->to($to)->getAvg($bucketSize, Data::BY_UNIFORM);
		
		$bucketDates = [];
		$bucketData = [];
		
		for($i = 0; $i < $bucketSize; $i++){
			if(!$bucket->get($i)){ break; }
			if(date($dateFormat, $bucket->getUTCFrom($i)) == date($dateFormat, $bucket->getUTCTo($i)-1)){
				$bucketDates[] = date($dateFormat, $bucket->getUTCFrom($i));
			} else {
				$bucketDates[] = date($dateFormat, $bucket->getUTCFrom($i)) . ' - ' . date($dateFormat, $bucket->getUTCTo($i)-1);
			}
			$bucketData[] = $bucket->getRating($i);
		}
		
		$milestones = [];
		$financials = [];
		
		$this->data['playground'] = [
			"aspects" => $aspects,
			"milestones" => $milestones,
			"financials" => $financials,
			"average" => [
				"labels" => $bucketDates,
				"bucket" => $bucketData
			],
			"minDate" => $minDate
		];
	}
}
?>