<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

/*
	Promo Codes
	Finance
	
	Account (Edit/Link to Corporate)
	Companies
	Stores
	
	Add/Remove Categories/Keywords/AspectTypes
	
	Tablets
	
	Stats
	
	Logs
*/
?>
