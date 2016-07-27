<?php
class DataTemplate
{
	private $params = [];
	private $html = '';
	
	function __construct($path = ''){
		if (!empty($path)){
			$path = "data_templates/{$path}.html";
			if (file_exists($path)){
				$this->html = file_get_contents($path);
			} else {
				return false;
			}
		}
		return true;
	}
	
	public function setHTML($html)
	{
		$this->html = $html;
	}
	
	public function set($key, $val = null)
	{
		if (is_array($key)){
			$this->params = array_merge($this->params, $key);
		} else {
			$this->params[$key] = $val;
		}
	}
	
	public function get($key)
	{
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}
	
	public function render()
	{
		// Replace {{key}} with val.
		return preg_replace_callback('/{{(([a-zA-Z]+[_0-9]*)+)}}/', function($matches){
			$match = strtolower($matches[1]);
			$val = $this->get($match);
			if ($val !== null){
				return $val;
			}
			return '';
		}, $this->html);
	}
	
	public function __toString()
	{
		return $this->render();
	}
	
	public function toJSON()
	{
		$object = [
			'params' => $this->params,
			'html' => $this->html
		];
		
		return json_encode($object);
	}
	
	public static function fromJSON($json)
	{
		if ($json === false || $json === null){ return false; }
		
		$json = json_decode($json, true);
		
		$dataTemp = new DataTemplate();
		$dataTemp->setHTML(isset($json['html']) ? $json['html'] : '');
		$dataTemp->set(isset($json['params']) ? $json['params'] : []);
		
		return $dataTemp;
	}
}
?>