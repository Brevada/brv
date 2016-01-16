<?php
if(!Brevada::IsLoggedIn() || !Permissions::has(Permissions::VIEW_STORE)){
	Brevada::Redirect('/logout');
}

$this->IsScript = true;

$store_id = $_SESSION['StoreID'];
$company_id = $_SESSION['CompanyID'];

if(empty($store_id) && empty($company_id)){ exit; }

$name = "BrevadaEmails.csv";
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="'.$name.'";');

$f = fopen('php://output', 'w');


if(!empty($store_id)){
	$line = array(__('Email Address'), __('Average Rating'));
	fputcsv($f, $line, ';');

	$query = Database::query("SELECT `subscriptions`.`EmailAddress`, `subscriptions`.`SessionCode`, IFNULL((SELECT AVG(`feedback`.`Rating`) FROM `feedback` WHERE `feedback`.SessionCode = `subscriptions`.SessionCode AND `feedback`.SessionCode IS NOT NULL AND `feedback`.`Rating` > -1), -1) as `AverageRating` FROM `subscriptions` WHERE `subscriptions`.`StoreID` = {$store_id}");
	if($query !== false){
		while($row = $query->fetch_assoc()){
			$line = array(__('Email Address') => $row['EmailAddress'], __('Average Rating') => ($row['AverageRating'] == -1 ? 'N/A' : ceil(floatval($row['AverageRating'])).'%'));
			fputcsv($f, $line, ';');
		}
	}	
} else if(!empty($company_id) && $_SESSION['Corporate'] && Permissions::has(Permissions::VIEW_COMPANY)){
	$line = array(__('Store Name'), __('Email Address'), __('Average Rating'));
	fputcsv($f, $line, ';');

	$query = Database::query("SELECT `stores`.`Name`, `subscriptions`.`EmailAddress`, `subscriptions`.`SessionCode`, IFNULL((SELECT AVG(`feedback`.`Rating`) FROM `feedback` WHERE `feedback`.SessionCode = `subscriptions`.SessionCode AND `feedback`.SessionCode IS NOT NULL AND `feedback`.`Rating` > -1), -1) as `AverageRating` FROM `subscriptions` LEFT JOIN `stores` ON `stores`.id = `subscriptions`.`StoreID` WHERE `stores`.`CompanyID` = {$company_id} ORDER BY `stores`.`Name` ASC");
	if($query !== false){
		while($row = $query->fetch_assoc()){
			$line = array(__('Store Name') => $row['Name'], __('Email Address') => $row['EmailAddress'], __('Average Rating') => ($row['AverageRating'] == -1 ? 'N/A' : ceil(floatval($row['AverageRating'])).'%'));
			fputcsv($f, $line, ';');
		}
	}	
}

exit;
?>