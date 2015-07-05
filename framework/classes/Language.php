<?php
/**
	Written by Noah Negin-Ulster
	
	Enables internationalization.
*/

/*
	locale = Locale to use if none requested via HTTP.
	ignoreHTTP = Whether to ignore HTTP language request.
*/
function setLocalization($locale = '', $ignoreHTTP = false)
{
	if(empty($locale) || !$ignoreHTTP){
		$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
	
	if(stripos($locale, 'fr') === 0){
		$locale = 'fr_CA';
	} else {
		$locale = 'en_CA';
	}
	
	setcookie('lang', $locale, time()+60*60*24*30, '/');
	$_SESSION['lang'] = $locale;
	
	Language::load();
}

function __($text) {
    return Language::get($text);
}

function _e($text){
	echo Language::get($text);
}

class Language
{
	private static $dictionary = array();
	
	public static function get($text)
	{
		if(isset(self::$dictionary[strtolower($text)])){
			return self::$dictionary[strtolower($text)];
		} else {
			return $text;
		}
	}
	
	public static function load()
	{
		$lang = $_SESSION['lang'];
		if($lang != 'fr_CA' && $lang != 'en_CA'){
			$lang = $en_CA;
		}
		
		$dictPath = "locale/{$lang}.po";
		if(file_exists($dictPath)){
			self::$dictionary = self::poToArray($dictPath);
		}
	}
	
	private static function poToArray($path)
	{
		$arrayFile = $path . '.php';
		if(file_exists($arrayFile)){
			return json_decode(file_get_contents($arrayFile), true);
		}
		
		$output = array();
		
		$po = file($path);
		$key = '';
		foreach($po as $line){
			if($line == 'msgid ""' || empty($line)){ continue; }
			if (substr($line,0,5) == 'msgid') {
				$key = trim(substr(trim(substr($line,5)),1,-1));
			}
			if($line == 'msgstr ""'){
				$key = '';
			} else {
				if (substr($line,0,6) == 'msgstr' && !empty($key)) {
					$value = trim(substr(trim(substr($line,6)),1,-1));
					if(!empty($value)){
						$output[strtolower($key)] = $value;
					}
					$key = '';
				}
			}
		}
		
		@file_put_contents($arrayFile, json_encode($output));
		return $output;
	}
}
?>