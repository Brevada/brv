<?php
class Geography
{
	public static function GetGeo($cache = true)
	{
		if($cache)
		{
			if(!empty($_SESSION['geo_ip']) && !empty($_SESSION['geo_country']) && !empty($_SESSION['geo_province']) && !empty($_SESSION['geo_city'])){
				return array('ip' => $_SESSION['geo_ip'], 'country' => $_SESSION['geo_country'], 'province' => $_SESSION['geo_province'], 'city' => $_SESSION['geo_city']);
			}
		}
		
		$result = array('ip' => '', 'country' => '', 'province' => '', 'city' => '');
		
		$ip = self::GetIP();
		
		$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

		if($ip_data && !empty($ip_data->geoplugin_countryName))
		{
			$_SESSION['geo_country'] = $result['country'] = $ip_data->geoplugin_countryName;
		}
		
		if($ip_data && !empty($ip_data->geoplugin_regionName))
		{
			$_SESSION['geo_province'] = $result['province'] = $ip_data->geoplugin_regionName;
		}
		
		if($ip_data && !empty($ip_data->geoplugin_city))
		{
			$_SESSION['geo_city'] = $result['city'] = $ip_data->geoplugin_city;
		}
		
		$_SESSION['geo_ip'] = $result['ip'] = $ip;
		
		return $result;
	}
	
	public static function GetIP()
	{
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
		
		return $ip;
	}
}
?>