<?php
class Logger
{

	public static function log($s, $level, $location='ANY')
	{
		$level = strtoupper($level);
		$s = strval($s);
		
		$filePath = 'logs/log '.date('d-m-Y').'.log';
		
		if(!file_exists('logs')){ @mkdir('logs', 0600); }
		
		$line = date('H:i:s')." - [{$level}]: {$s}\n";
		
		if(!file_exists($filePath)){
			$fp = fopen($filePath, 'a');
			fwrite($fp, $line);
			fclose($fp);
			chmod($filePath, 0600);
		} else {
			file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);
		}
		
	}

	public static function debug($s, $location='ANY')
	{
		if(!DEBUG){ return; }
		self::log($s, 'DEBUG', $location);
	}
	
	public static function info($s, $location='ANY')
	{
		self::log($s, 'INFO', $location);
	}
	
	public static function warning($s, $location='ANY')
	{
		self::log($s, 'WARNING', $location);
	}
	
	public static function severe($s, $location='ANY')
	{
		self::log($s, 'SEVERE', $location);
	}
}
?>