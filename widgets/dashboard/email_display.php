<?php
$store_id = $this->getParameter('store_id');
$company_id = $this->getParameter('company_id');

if(!Brevada::IsLoggedIn() || !Permissions::has(Permissions::VIEW_STORE)){ exit; }
?>
<div class="email-display-container">
		<div class="slide-down-header">
			<div class="slide-down-button"><a href='/dashboard/exportemails.php' style='color:black;'><?php _e("Export to CSV"); ?></a></div>
			<div id="email-close" class="slide-down-button"><?php _e("Close"); ?></div>
		</div>
<?php
$sql = '';

if(empty($store_id) && !empty($company_id)){
	$sql = "SELECT `stores`.`Name`, `subscriptions`.`EmailAddress`, `subscriptions`.`SessionCode`, IFNULL((SELECT AVG(`feedback`.`Rating`) FROM `feedback` WHERE `feedback`.SessionCode = `subscriptions`.SessionCode AND `feedback`.SessionCode IS NOT NULL AND `feedback`.`Rating` > -1), -1) as `AverageRating` FROM `subscriptions` LEFT JOIN `stores` ON `stores`.id = `subscriptions`.`StoreID` WHERE `stores`.`CompanyID` = {$company_id} ORDER BY `stores`.`Name` ASC";
} else if(!empty($store_id)){
	$sql = "SELECT `subscriptions`.`EmailAddress`, `subscriptions`.`SessionCode`, IFNULL((SELECT AVG(`feedback`.`Rating`) FROM `feedback` WHERE `feedback`.SessionCode = `subscriptions`.SessionCode AND `feedback`.SessionCode IS NOT NULL AND `feedback`.`Rating` > -1), -1) as `AverageRating` FROM `subscriptions` WHERE `subscriptions`.`StoreID` = {$store_id}";
}

$query = Database::query($sql);

while($row = $query->fetch_assoc()){
	$rating = $row['AverageRating'] == -1 ? -1 : ceil(floatval($row['AverageRating']));
	
	if($rating > -1){
		$modifier = 'neutral';
		if($rating >= 80){
			$modifier = 'positive';
		} else if($rating >= 60){
			$modifier = 'great';
		} else if($rating >= 40){
			$modifier = 'neutral';
		} else if($rating >= 20){
			$modifier = 'bad';
		} else {
			$modifier = 'negative';
		}
		
		$rating = "{$rating}%";
	} else {
		$rating = "N/A";
		$modifier = 'neutral';
	}
	
	echo "<div class='email'><div class='rating {$modifier}'>{$rating}</div><div class='text'>{$row['EmailAddress']}</div></div>";
}
?>
</div>