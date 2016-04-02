<?php
class TaskLive extends AbstractTask
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
		
		
		/*$breakdown = [
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
		];*/
		
		$HOUR = 3600; $DAY = $HOUR * 24; $WEEK = $DAY * 7; $MONTH = 52*$WEEK / 12;
		
		/* Scores */
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
		
		$scores = [];
		foreach($rows as $row){
			$score = (new Data())->store($store)->from(time()-$WEEK)->to(time())->aspectType($row['AspectTypeID'])->getAvg();
			$scores[] = [
				'title' => $row['Title'],
				'percent' => round($score->getRating()),
				'id' => $row['id']
			];
		}
		
		$day_up = ['Ambience', 'Customer Service'];
		$day_down = ['Customer Service', 'Food Quality'];
		$day_average = 40;
		$day_change = 82;
		$day_responses = 4520;
		$day_bucket = ['data'=>[30, 20, 40, 23, 8]];
		
		$week_up = ['Ambience', 'Customer Service'];
		$week_down = ['Customer Service', 'Food Quality'];
		$week_average = 40;
		$week_change = 82;
		$week_responses = 4520;
		$week_bucket = ['data'=>[30, 20, 40, 23, 8]];
		
		$all_up = ['Ambience', 'Customer Service'];
		$all_down = ['Customer Service', 'Food Quality'];
		$all_average = 40;
		$all_responses = 4520;
		$all_bucket = ['data'=>[30, 20, 40, 23, 8]];
		
		$feed = [
			['percent' => rand(20,100), 'aspect' => 'Customer Service', 'date' => 'March 17th, 12:25pm', 'medium' => rand(0,1) == 1 ? 'desktop' : 'tablet']
		];
		
		$this->data['live'] = [
			'snapshot' => [
				'day' => [
					'up' => $day_up,
					'down' => $day_down,
					'average' => $day_average,
					'change' => $day_change,
					'responses' => $day_responses,
					'bucket' => $day_bucket
				],
				'week' => [
					'up' => $week_up,
					'down' => $week_down,
					'average' => $week_average,
					'change' => $week_change,
					'responses' => $week_responses,
					'bucket' => $week_bucket
				],
				'all' => [
					'up' => $all_up,
					'down' => $all_down,
					'average' => $all_average,
					'responses' => $all_responses,
					'bucket' => $all_bucket
				]
			],
			'scores' => $scores,
			'feed' => $feed
		];
	}
}
?>