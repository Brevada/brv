<?php
/*
	Written by Noah Negin-Ulster
	Brevada Functions
*/

require_once 'packages/mobile_detect/Mobile_Detect.php';

//VALIDATE constants
define('VALIDATE_TRIM', 0);
define('VALIDATE_DATABASE', 1);

class Brevada
{
	private static $mobileDetector;

	//Can only validate integers and strings
	public static function validate($v, $type = 0)
	{
		$v = isset($v) ? $v : '';
		$v = is_int($v) ? $v : trim($v);
		
		if(!is_int($v) && ($type & VALIDATE_DATABASE)){
			$v = Database::escape_string($v);
		}
		
		return $v;
	}
	
	public static function Redirect($to)
	{
		header("Location: {$to}");
		exit;
	}
	
	public static function IsLoggedIn()
	{
		return isset($_SESSION['user_id']) && $_SESSION['user_id'] != 'none';
	}
	
	public static function IsMobile()
	{
		if(!isset(Brevada::$mobileDetector))
		{
			Brevada::$mobileDetector = new Mobile_Detect;
		}
		
		if(isset($_SESSION['desktopMode']) && $_SESSION['desktopMode'] == true)
		{
			return false;
		}
		
		return Brevada::$mobileDetector->isMobile();
	}
	
	public static function GetGeo($cache = true)
	{
		if($cache)
		{
			if(!empty($_SESSION['geo_ip']) && !empty($_SESSION['geo_country'])){
				return array('ip' => $_SESSION['geo_ip'], 'country' => $_SESSION['geo_country']);
			}
		}
		
		$result = array('ip' => '', 'country' => '');
		
		$client  = empty($_SERVER['HTTP_CLIENT_IP']) ? '' : $_SERVER['HTTP_CLIENT_IP'];
		$forward = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? '' : $_SERVER['HTTP_X_FORWARDED_FOR'];
		$ip  = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
		
		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		else if(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		
		$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

		if($ip_data && $ip_data->geoplugin_countryName != null)
		{
			$result['country'] = $ip_data->geoplugin_countryName;
			$_SESSION['geo_country'] = $result['country'];
		}
		
		$result['ip'] = $ip;
		$_SESSION['geo_ip'] = $result['ip'];
		
		return $result;
	}
	
	public static function IsInternetExplorer()
	{
		return preg_match('/(?i)msie [1-9]/', $_SERVER['HTTP_USER_AGENT']);
	}
	
	public static function FromPOSTGET($v)
	{
		return empty($_POST[$v]) ? (empty($_GET[$v]) ? '' : $_GET[$v]) : $_POST[$v];
	}
	
}
?>