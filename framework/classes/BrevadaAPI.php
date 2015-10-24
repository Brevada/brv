<?php
abstract class AbstractAPI
{
	abstract protected function executeTask($method, $task);
	protected $data = [];
	
	protected function sendHeaders()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, PUT, GET');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Content-Type');
		header('Content-type: application/json');
	}
	
	protected function verifyHeaders($method)
	{
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if(stripos($userAgent, TABLET_USERAGENT) !== 0){ return false; }
		
		return $method !== null && strcasecmp($method, $_SERVER['REQUEST_METHOD']) === 0;
	}
	
	protected function sendData()
	{
		echo json_encode($this->data);
	}
	
	protected function addError($error)
	{
		if(empty($this->data['error'])){
			$this->data['error'] = array();
		}
		
		$this->data['error'][] = $error;
	}
	
	public function process($version, $method, $task)
	{
		$this->sendHeaders();
		if($this->verifyHeaders($method)){
			$this->executeTask($method, $task);
		} else {
			$this->addError('Invalid headers.');
		}
		$this->sendData();
	}
}

class BrevadaAPI extends AbstractAPI
{
	protected function executeTask($method, $task)
	{
		try {
			TaskLoader::load($task)->execute(strtolower($method), explode('/', $task), $this->data);
		} catch (Exception $e) {
			$this->addError($e->getMessage());
		}
	}
}
?>