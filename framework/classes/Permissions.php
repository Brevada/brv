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
}
?>