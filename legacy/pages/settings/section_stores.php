<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}

$numStores = 0;
$maxStores = 1;

if(($query = Database::query("SELECT `company_features`.`MaxStores` FROM `company_features` LEFT JOIN `companies` ON `companies`.FeaturesID = `company_features`.id WHERE `companies`.id = {$_SESSION['CompanyID']} LIMIT 1"))!==false){
	$maxStores = @intval($query->fetch_assoc()['MaxStores']);
}

if($maxStores == 0){ $maxStores = 1; }

if(($query = Database::query("SELECT `stores`.`Name`, `stores`.URLName FROM `stores` WHERE `stores`.CompanyID = {$_SESSION['CompanyID']} ORDER BY `stores`.`Name` ASC")) !== false){
	$numStores = $query->num_rows;
}

if($numStores > $maxStores){ $maxStores = $numStores; }
?>
<form id='frmAccount' action='settings?section=logins' method='post'>
<div class='form-account'>
	<span class="form-subheader"><?php echo sprintf(__("You are using %s out of your %s available stores."), '<span class="large">'.$numStores.'</span>', '<span class="large">'.$maxStores.'</span>'); ?></span><br />
	
	<table class='table table-white table-bordered table-hover table-data'>
		<thead>
			<th><?php _e("Store Name"); ?></th>
			<th><?php _e("Store URL"); ?></th>
		</thead>
		<tbody>
			<?php
				if($query !== false && $query->num_rows > 0){
					while($row = $query->fetch_assoc()){
						$name = ucwords($row['Name']);
						$url = URL . $row['URLName'];
			?>
						<tr>
							<td><?php echo $name; ?></td>
							<td><a target='_blank' href='<?php echo $url; ?>'><?php echo $url; ?></a></td>
						</tr>
			<?php
					}
				}
			?>
		</tbody>
	</table>
</div>
</form>