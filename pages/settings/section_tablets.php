<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}

$numTablets = 0;
$maxTablets = 1;

if(($query = Database::query("SELECT `company_features`.`MaxTablets` FROM `company_features` LEFT JOIN `companies` ON `companies`.FeaturesID = `company_features`.id WHERE `companies`.id = {$_SESSION['CompanyID']} LIMIT 1"))!==false){
	$maxTablets = @intval($query->fetch_assoc()['MaxTablets']);
}

if(($query = Database::query("SELECT `tablets`.`id`, `tablets`.`Status`, `stores`.`Name` as StoreName FROM `tablets` LEFT JOIN `stores` ON `stores`.id = `tablets`.`StoreID` WHERE `stores`.CompanyID = {$_SESSION['CompanyID']} ORDER BY `tablets`.`id` ASC")) !== false){
	$numTablets = $query->num_rows;
}

$message = '';
if(isset($_GET['thanks'])){
	$message = __("Thank you for reporting a broken tablet. We will look into it and get back to you shortly.");
}
?>
<?php if(!empty($message)){ echo "<p class='message'>{$message}</p>"; } ?>
<form id='frmAccount' action='settings?section=logins' method='post'>
<div class='form-account'>
	<span class="form-header"><?php echo sprintf(__("You are using %s out of your %s available tablets."), '<span class="large">'.$numTablets.'</span>', '<span class="large">'.$maxTablets.'</span>'); ?></span><br />
	
	<table class='table table-white table-bordered table-hover table-data'>
		<thead>
			<th><?php _e("Tablet ID"); ?></th>
			<th><?php _e("Store"); ?></th>
			<th><?php _e("Status"); ?></th>
		</thead>
		<tbody>
			<?php
				if($query !== false && $query->num_rows > 0){
					while($row = $query->fetch_assoc()){
						$id = $row['id'];
						$storeName = $row['StoreName'];
						$status = $row['Status'];
			?>
						<tr>
							<td>#<?php echo $id; ?></td>
							<td><?php echo $storeName; ?></td>
							<td><?php _e(ucwords($status)); ?></td>
						</tr>
			<?php
					}
				}
			?>
		</tbody>
	</table>
	
	<span class="form-subheader"><a href='/settings/reporttablet.php'><?php _e("Tablet broken? Click here to report it."); ?></a></span>
</div>
</form>