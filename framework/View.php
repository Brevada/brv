<?php
/**
Framework
Developed by Noah Negin-Ulster
//**/

define('DEFAULT_TITLE', 'Brevada Feedback Platform');

class View
{
	private $Content = '';
	
	private $Resources = array();
	
	public $DocType = false;
	public $RootPage = false;
	public $IsScript = false;
	
	public $Title = '';
	
	public $Parameters = array();
	
	public function __construct($page = '', $param = array())
	{
	
		if(!empty($param) && is_array($param))
		{
			$this->Parameters = $param;
		}
	
		if(!empty($page))
		{
			$this->loadContent($page);
		}
	}
	
	public function setTitle($t)
	{
		$this->Title = $t;
	}
	
	public function getParameter($key)
	{
		return empty($this->Parameters[$key]) ? '' : $this->Parameters[$key];
	}
	
	/// Compile View
	public function printView()
	{
		if($this->DocType){
			echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n";
		}
		
		if($this->RootPage){
			echo "<html>\n<head>\n".$this->getResources()."\n";
			if(empty($this->Title)){ $this->Title = DEFAULT_TITLE; }
			echo "<title>{$this->Title}</title>\n";
			echo "</head>\n<body>\n{$this->Content}\n</body>\n</html>";
		} else {
			echo $this->Content;
		}
	}
	
	public function __toString()
	{
		if(ob_start())
		{
			ob_clean();
			$this->printView();
			$rendered = ob_get_contents();
			ob_end_clean();
			if($rendered !== false)
			{
				return $rendered;
			}
		}
		return '';
	}
	
	/**
		Load external pages.
	//**/
	
	private function loadPage($page)
	{
		if(file_exists($page)){
			if(ob_start()){
				ob_clean();
				include $page;
				$rendered = ob_get_contents();
				ob_end_clean();
				if($rendered !== false){
					return $rendered;
				}
			}
		}
		return '';
	}
	
	public function loadContent($page){
		if(!empty($page) && file_exists($page)){
			$this->Content = $this->loadPage($page);
		}
	}
	
	private function filterResources($var)
	{
		return !in_array($var, $this->Resources);
	}
	
	public function addResource($resource, $prepend = false, $raw = false)
	{
		if(is_array($resource)) {
			if($prepend){
				$this->Resources = array_merge(array_filter($resource, array($this, 'filterResources')), $this->Resources);
			} else {
				$this->Resources = array_merge($this->Resources, array_filter($resource, array($this, 'filterResources')));
			}
		} else {
			if($raw === false && (file_exists($resource) || file_exists("../{$resource}"))) {
				$pi = @pathinfo($resource);
				if(isset($pi) && !empty($pi['extension'])){
					if($pi['extension'] == 'js'){
						$resource = "<script type='text/javascript' src='{$resource}'></script>";
					} else if($pi['extension'] == 'css') {
						$resource = "<link href='{$resource}' rel='stylesheet' type='text/css' />";
					} else if($pi['extension'] == 'php') {
						if(ob_start()){
							ob_clean();
							include $resource;
							$fi = ob_get_contents();
							if($fi !== false){
								$fileArray = preg_split("/\\r\\n|\\r|\\n/", $fi);
								if(!empty($fileArray)){
									if($prepend){
										$this->Resources = array_merge(array_filter($fileArray, array($this, 'filterResources')), $this->Resources);
									} else {
										$this->Resources = array_merge($this->Resources, array_filter($fileArray, array($this, 'filterResources')));
									}
								}
							}
							ob_end_clean();
						}
						return;
					}
				}
			}
			
			if(!empty($resource) && !in_array($resource, $this->Resources)){
				if($prepend){
					array_unshift($this->Resources, $resource);
				} else {
					$this->Resources[] = $resource;
				}
			}
		}
	}
	
	public function getResources($asArray = false){
		if($asArray){
			return $this->Resources;
		} else {
			$result = '';
			if(!empty($this->Resources)){
				foreach($this->Resources as $Res){
					$result .= $Res;
				}
			}
			return $result;
		}
	}
	
	public function add($view, $param = array())
	{	
		$this->addResource($view->getResources(true));
		$this->Title = !empty($view->Title) ? $view->Title : $this->Title;
		$view->printView();
	}
	
}
?>