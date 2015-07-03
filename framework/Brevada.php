<?php
/*
	Written by Noah Negin-Ulster
	Brevada Functions
*/

require_once 'packages/mobile_detect/Mobile_Detect.php';
require_once 'classes/password.php';

//VALIDATE constants
define('VALIDATE_TRIM', 0);
define('VALIDATE_DATABASE', 1);

/* LOGIN_TIMEOUT = 0; to disable timeout. */
define('LOGIN_TIMEOUT', 2*3600);

define('TABLET_USERAGENT', 'BrevadaTablet');

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
		if(strpos($to, '/') === 0){ $to = substr($to, 1); }
		$to = ROOT_PATH . $to;
		header("Location: {$to}");
		exit;
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
	
	public static function IsInternetExplorer()
	{
		return preg_match('/(?i)msie [1-9]/', $_SERVER['HTTP_USER_AGENT']);
	}
	
	public static function FromPOSTGET($v)
	{
		return empty($_POST[$v]) ? (empty($_GET[$v]) ? '' : $_GET[$v]) : $_POST[$v];
	}
	
	/* Account Connection */
	
	public static function IsLoggedIn()
	{
		if(!empty($_SESSION['time']) && (LOGIN_TIMEOUT == 0 || time() - intval($_SESSION['time']) < LOGIN_TIMEOUT) && !empty($_SESSION['user_id'] && $_SESSION['user_id'] != 'none') && !empty($_SESSION['ip']) && $_SESSION['ip'] == $_SERVER['REMOTE_ADDR']){
			$_SESSION['time'] = time();
			return true;
		} else {
			self::Logout();
			return false;
		}
	}
	
	public static function Logout()
	{
		$_SESSION['user_id'] = "none";
		$_SESSION['corporate'] = "";
		$_SESSION['corporate_id'] = "none";
		unset($_SESSION['time']);
		unset($_SESSION['ip']);
	}
	
	
	/*
		If password length == 60, assume it is a hashed password;
		otherwise assume it is an old plaintext.
	*/
	public static function LogIn($email, $password)
	{
		$email = Database::escape_string(strtolower(trim($email)));
		if(($query = Database::query("SELECT id, email, password FROM `users` WHERE `email` = '{$email}' LIMIT 1")) !== false){
			if($query !== false){
				if($query->num_rows > 0){
					while($row = $query->fetch_assoc()){
						if(strlen($row['password']) == 60){
							//Treat like hashed.
							if(!password_verify($password, $row['password'])){
								return false;
							}
						} else {
							//Treat as raw.
							if($password != $row['password']){
								return false;
							}
						}
						$_SESSION['user_id'] = $row['id'];
					}
					$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
					$_SESSION['time'] = time();
					return true;
				}
			}
		}
		return false;
	}
	
	public static function HashPassword($password)
	{
		//Return a *60* character hashed password.
		//cost is the CPU cost (4 - 31). The higher the number, the better.
		
		return password_hash($password, PASSWORD_BCRYPT, array('cost' => 10));
	}
	
}
?>