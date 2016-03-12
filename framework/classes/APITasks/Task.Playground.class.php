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
		
		$from = max(@intval(Brevada::FromGET('from')), time()-$MONTH*12*5);
		$to = @intval(Brevada::FromGET('to'));
		if($to == 0){ $to = time(); }
		$included = empty($_GET['included']) ? [] : array_map('intval', explode(',', $_GET['included']));
		if(!isset($_GET['included'])){ $included = false; }
		
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
		
		$includedTypes = [];
		
		$bucketSize = 12;
		$interval = floor($delta / $bucketSize);
		
		$aspects = [];
		foreach($rows as $row){			
			$aspectType = $row['AspectTypeID'];
			
			if($included !== false && !in_array(intval($row['id']), $included)){
				$aspects[] = [
					"id" => $row['id'],
					"title" => __($row['Title'])
				];
				continue;
			}
			
			$includedTypes[] = intval($aspectType);
			
			$overall = (new Data())->store($store)->aspectType($aspectType)->getAvg();
			$minDate = min($overall->getUTCFrom(), $minDate);
			
			$rating = (new Data())->store($store)->aspectType($aspectType)->from($from)->to($to)->getAvg();
			
			$bucketDates = [];
			$bucketData = [];
			
			$minBucket = 0;
			$maxBucket = 0;
			
			$prevVal = (new Data())->store($store)->aspectType($aspectType)->from($from-$interval)->to($from)->getAvg()->getRating();
			for($i = 0; $i < $bucketSize; $i++){
				$intvStart = $from + $i*$interval;
				$intvEnd = $from + ($i+1)*$interval - 1;
				
				if(date($dateFormat, $intvStart) == date($dateFormat, $intvEnd)){
					$bucketDates[] = date($dateFormat, $intvStart);
				} else {
					$bucketDates[] = date($dateFormat, $intvStart) . ' - ' . date($dateFormat, $intvEnd);
				}
				
				$intvRating = (new Data())->store($store)->aspectType($aspectType)->from($intvStart)->to($intvEnd)->getAvg();
				
				if($intvRating->getSize() > 0){
					$bucketData[] = $intvRating->getRating() - $prevVal;
					$prevVal = $intvRating->getRating();
				} else {
					$bucketData[] = 0;
				}
				
				$minBucket = min($minBucket, $prevVal);
				$maxBucket = max($maxBucket, $prevVal);
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
					"average" => $rating->getRating(),
					"min" => $minBucket,
					"max" => $maxBucket
				]
			];
		}
		
		$bucketDates = [];
		$bucketData = [];
		
		$minBucket = 0;
		$maxBucket = 0;
		
		if(!empty($includedTypes)){
			$prevVal = (new Data())->store($store)->aspectType($includedTypes)->from($from-$interval)->to($from)->getAvg()->getRating();
			for($i = 0; $i < $bucketSize; $i++){
				$intvStart = $from + $i*$interval;
				$intvEnd = $from + ($i+1)*$interval - 1;
				
				if(date($dateFormat, $intvStart) == date($dateFormat, $intvEnd)){
					$bucketDates[] = date($dateFormat, $intvStart);
				} else {
					$bucketDates[] = date($dateFormat, $intvStart) . ' - ' . date($dateFormat, $intvEnd);
				}
				
				$intvRating = (new Data())->store($store)->aspectType($includedTypes)->from($intvStart)->to($intvEnd)->getAvg();
				
				if($intvRating->getSize() > 0){
					$bucketData[] = $intvRating->getRating() - $prevVal;
					$prevVal = $intvRating->getRating();
				} else {
					$bucketData[] = 0;
				}
				
				$minBucket = min($minBucket, $prevVal);
				$maxBucket = max($maxBucket, $prevVal);
			}
		}
		
		$milestones = [];
		$financials = [];
		
		$this->data['playground'] = [
			"aspects" => $aspects,
			"milestones" => $milestones,
			"financials" => $financials,
			"average" => [
				"labels" => $bucketDates,
				"bucket" => $bucketData,
				"min" => $minBucket,
				"max" => $maxBucket
			],
			"minDate" => $minDate
		];
	}
}
?>