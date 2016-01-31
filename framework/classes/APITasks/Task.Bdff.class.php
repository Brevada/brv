<?php
class TaskBdff extends AbstractTask
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
}
?>