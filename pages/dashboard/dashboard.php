<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }

if(Permissions::has(Permissions::VIEW_ADMIN)){
	Brevada::Redirect('/admin');
}

$store_id = Brevada::FromGET('s');

if(empty($store_id) && !$_SESSION['Corporate'] && Permissions::has(Permissions::VIEW_STORE)){
	$this->add(new View('../pages/dashboard/dashboard_store.php', array('valid' => true)));
} else if(!empty($store_id) && $_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) {
	$store_id = @intval($store_id);
	$company_id = @intval($_SESSION['CompanyID']);
	
	if(($check = Database::query("SELECT 1 FROM `stores` LEFT JOIN `companies` ON `companies`.id = `stores`.CompanyID WHERE `stores`.id = {$store_id} AND `companies`.id = {$company_id} LIMIT 1")) !== false){
		if($check->num_rows == 0){ Brevada::Redirect('/home/logout'); }
	} else { Brevada::Redirect('/home/logout'); }
	
	$_SESSION['StoreID'] = $store_id;
	$this->add(new View('../pages/dashboard/dashboard_store.php', array('valid' => true)));
} else if(empty($store_id) && $_SESSION['Corporate'] && Permissions::has(Permissions::VIEW_COMPANY)){
	$_SESSION['StoreID'] = null;
	$this->add(new View('../pages/dashboard/dashboard_corporate.php', array('valid' => true)));
} else { Brevada::Redirect('/404?page'); }
?>