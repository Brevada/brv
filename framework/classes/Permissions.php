<?php
class Permissions
{
	const LOGIN_ACCESS = 1;
	const VIEW_STORE = 2;
	const MODIFY_STORE = 3;
	const VIEW_COMPANY = 80;
	const MODIFY_COMPANY_STORES = 81;
	const MODIFY_COMPANY = 82; /* Default. */
	const VIEW_ADMIN = 253;
	const EDIT_ADMIN = 254;
	const FULL_ADMIN = 255;
	
	public static function get()
	{
		if(empty($_SESSION['Permissions'])){ return 0; }
		return intval($_SESSION['Permissions']);
	}
	
	public static function has($i)
	{
		return self::get() >= $i;
	}
	
	public static function translate($i)
	{
		switch($i)
		{
			case self::LOGIN_ACCESS:
				return 'LOGIN_ACCESS';
			case self::VIEW_STORE:
				return 'VIEW_STORE';
			case self::MODIFY_STORE:
				return 'MODIFY_STORE';
			case self::VIEW_COMPANY:
				return 'VIEW_COMPANY';
			case self::MODIFY_COMPANY_STORES:
				return 'MODIFY_COMPANY_STORES';
			case self::MODIFY_COMPANY:
				return 'MODIFY_COMPANY';
			case self::VIEW_ADMIN:
				return 'VIEW_ADMIN';
			case self::EDIT_ADMIN:
				return 'EDIT_ADMIN';
			case self::FULL_ADMIN:
				return 'FULL_ADMIN';
			default:
				return 'UNKNOWN';
		}
	}
}
?>