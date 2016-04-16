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
			SELECT COUNT(*) as cnt FROM tablets WHERE StoreID = ? AND `Status` = 'At Store'")) !== false){
			$stmt->bind_param('i', $store);
			if($stmt->execute()){
				$stmt->bind_result($tablets);
				$stmt->fetch();
			}
			$stmt->close();
		}
		
		$online_tablets = 0;
		$since = time()-(60*15);
		if(($stmt = Database::prepare("
			SELECT COUNT(*) as cnt FROM tablets WHERE StoreID = ? AND `Status` = 'At Store' AND `OnlineSince` > ?")) !== false){
			$stmt->bind_param('ii', $store, $since);
			if($stmt->execute()){
				$stmt->bind_result($online_tablets);
				$stmt->fetch();
			}
			$stmt->close();
		}
		
		$this->data['hoverpod'] = [
			'tablets' => [
				'online' => $online_tablets,
				'total' => $tablets
			],
			'responses' => (new Data())->store($store)->from(time()-3600)->getAvg()->getSize(),
			'mood' => (new Data())->store($store)->from(time()-(12*3600))->getAvg()->getRating()
		];
	}
}
?>