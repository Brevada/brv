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
		}
		if(!Brevada::IsLoggedIn()){
			throw new Exception("Authentication required.");
		}
		$this->data = &$data;
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
			'responses' => (new Data())->store($store)->from(time()-3600)->getAvg()->getSize(),
			'mood' => (new Data())->store($store)->from(time()-(12*3600))->getAvg()->getRating()
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
			`stores`.`CompanyID` = ? AND
			`companies`.`Active` = 1 AND
			`companies`.`ExpiryDate` IS NOT NULL AND
			`companies`.`ExpiryDate` > NOW()
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
				"title" => __($row['Title']),
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
				"industry" => (new Data())->aspectType($aspectType)->keyword($keywords)->getAvg()->getRating()
			];
		}
		
		$this->data['aspects'] = $aspects;		
	}
	
	public function taskLive(){
		$store = @intval(Brevada::FromGET('store'));
		if(empty($store)){
			throw new Exception("Incomplete request: 'store' required.");
		}
		
		if(!$_SESSION['Corporate'] && $store != $_SESSION['StoreID']){
			throw new Exception("Invalid 'store'.");
		}
		
		$company = $_SESSION['CompanyID'];
		
		$hours = max(@intval(Brevada::FromGET('hours')), 1);
		
		$current = (new Data())->store($store)->from(time()-(3600*$hours))->getAvg();
		
		$breakdown = [
			'labels' => [
				'Positive', 'Great', 'Neutral', 'Bad', 'Negative'
			],
			'datasets' => [[
				'data' => [
					$current->get()[Data::TOTAL_FIVE_STAR],
					$current->get()[Data::TOTAL_FOUR_STAR],
					$current->get()[Data::TOTAL_THREE_STAR],
					$current->get()[Data::TOTAL_TWO_STAR],
					$current->get()[Data::TOTAL_ONE_STAR]
				],
				'backgroundColor' => [
					'#2ecc0e', '#82cc0e', '#afcc0e', '#ccc10e', '#cc750e'
				]
			]]
		];
		
		$previous = (new Data())->store($store)->to(time()-(3600*$hours))->getAvg();
		
		$change = $current->getSize() > 0 && $previous->getSize() > 0 ? $current->getRating() - $previous->getRating() : 'N/A';
		$parity = $current->getSize() > 0 && $previous->getSize() > 0 ? ($change > 0 ? '+' : ($change == 0 ? '' : '-')) : '';
		
		$current_day = (new Data())->store($store)->from(time()-(3600*24))->getAvg();
		$previous_day = (new Data())->store($store)->to(time()-(3600*24))->getAvg();
		$change_day = $current_day->getSize() > 0 && $previous_day->getSize() > 0 ? $current_day->getRating() - $previous_day->getRating() : 'N/A';
		
		
		$this->data['live'] = [
			'datastatus' => [
				'timeframe' => "{$hours} hours",
				'current_time' => date('g:i:m a')
			],
			'responses' => $current->getSize(),
			'score' => $current->getRating(),
			'change' => [
				'parity' => $parity,
				'change' => $change
			],
			'breakdown' => $breakdown,
			'newsfeed' => 'Newsfeed...',
			'daily' => [
				'score' => $current_day->getRating(),
				'change' => $change_day,
				'responses' => $current_day->getSize()
			]
		];
	}
	
	public function taskSupport(){
		$accountID = $_SESSION['AccountID'];
		$company = $_SESSION['CompanyID'];
		$message = Brevada::FromPOST('message');
		
		if(empty($message)){
			throw new Exception('Support message cannot be empty.');
		}
		
		if(strlen($message) > 1000){
			throw new Exception('Support message cannot be greater than 1000 characters.');
		}
		
		$insert_id = -1;
		if(($stmt = Database::prepare("INSERT INTO `support` (`AccountID`, `Date`, `Message`) VALUES (?, NOW(), ?)")) !== false){
			$stmt->bind_param('is', $accountID, $message);
			if(!$stmt->execute()){
				$insert_id = $stmt->insert_id;
				$stmt->close();
				throw new Exception("Unknown error.");
			}
			$stmt->close();
		}
		
		$company_name = ''; $company_phone = '';
		$first_name = ''; $email = '';
		
		if(($stmt = Database::prepare("
			SELECT companies.`Name`, companies.`PhoneNumber`, `accounts`.`FirstName`, `accounts`.`EmailAddress` FROM `companies` JOIN `accounts` ON `accounts`.`CompanyID` = `companies`.`id` WHERE companies.`id` = ? AND `accounts`.`id` = ? LIMIT 1")) !== false){
			$stmt->bind_param('ii', $company, $accountID);
			if($stmt->execute()){
				$stmt->bind_result($company_name, $company_phone, $first_name, $email);
				$stmt->fetch();
			}
			$stmt->close();
		}
		
		$encoded = htmlentities($message);
		
		$fields = [];
		
		$fields[] = [ 'title' => 'Company', 'value' => $company_name, 'short' => false ];
		if(!empty($first_name)){
			$fields[] = [ 'title' => 'Name', 'value' => $first_name, 'short' => true ];
		}
		if(!empty($company_phone)){
			$fields[] = [ 'title' => 'Phone #', 'value' => $company_phone, 'short' => true ];
		}
		if(!empty($email)){
			$fields[] = [ 'title' => 'Email', 'value' => $email, 'short' => true ];
		}
		
		$fields[] = [ 'title' => 'Message', 'value' => $encoded, 'short' => false ];
		
		Slack::send([
			'username' => 'BrevadaSupport',
			'channel' => '#support',
			'attachments' => [
				[
					'fallback' => "New support ticket: <".URL."admin?show=support&id={$insert_id}|View in Browser>",
					'pretext' => "New support ticket: <".URL."admin?show=support&id={$insert_id}|View in Browser>",
					'color' => '#FF2B2B',
					'fields' => $fields
				]
			]
		]);
	}
	
	public function taskIssue(){
		$accountID = $_SESSION['AccountID'];
		
		$message = Brevada::FromPOST('message');
		$supportID = Brevada::FromPOST('sid');
		
		if(!Permissions::has(Permissions::VIEW_ADMIN)){
			throw new Exception('Invalid authentication.');
		}
		
		if(empty($message)){
			throw new Exception('Support message cannot be empty.');
		}
		
		if(strlen($message) > 1000){
			throw new Exception('Support message cannot be greater than 1000 characters.');
		}
		
		if(strtolower($message) == '/closed'){
			if(($stmt = Database::prepare("UPDATE `support` SET `support`.`Resolved` = 1 WHERE `support`.`id` = ?")) !== false){
				$stmt->bind_param('i', $supportID);
				if(!$stmt->execute()){
					$stmt->close();
					throw new Exception("Unknown error.");
				}
				$stmt->close();
			}
			if(($stmt = Database::prepare("INSERT INTO `support_responses` (`SupportID`, `AccountID`, `Date`, `Message`) VALUES (?, ?, NOW(), ?)")) !== false){
				$message = 'The support ticket has been closed.';
				$stmt->bind_param('iis', $supportID, $accountID, $message);
				if(!$stmt->execute()){
					$stmt->close();
					throw new Exception("Unknown error.");
				}
				$stmt->close();
			}
		} else {
			if(($stmt = Database::prepare("INSERT INTO `support_responses` (`SupportID`, `AccountID`, `Date`, `Message`) VALUES (?, ?, NOW(), ?)")) !== false){
				$stmt->bind_param('iis', $supportID, $accountID, $message);
				if(!$stmt->execute()){
					$stmt->close();
					throw new Exception("Unknown error.");
				}
				$stmt->close();
			}
		}
	}
}
?>