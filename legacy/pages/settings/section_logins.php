<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}

$numAccounts = 0;
$maxAccounts = 1;

if(($query = Database::query("SELECT `company_features`.`MaxAccounts` FROM `company_features` LEFT JOIN `companies` ON `companies`.FeaturesID = `company_features`.id WHERE `companies`.id = {$_SESSION['CompanyID']} LIMIT 1"))!==false){
	$maxAccounts = @intval($query->fetch_assoc()['MaxAccounts']);
}

if($maxAccounts == 0){ $maxAccounts = 1; }

if(($query = Database::query("SELECT `accounts`.id as AccountID, `accounts`.`EmailAddress`, `accounts`.`StoreID`, `stores`.`Name` as StoreName, `accounts`.`Permissions` FROM `accounts` LEFT JOIN `stores` ON `stores`.id = `accounts`.StoreID WHERE `accounts`.`CompanyID` = {$_SESSION['CompanyID']} ORDER BY `EmailAddress` ASC")) !== false){
	$numAccounts = $query->num_rows;
}

$stores = array();
if(($storeQuery = Database::query("SELECT `stores`.`Name`, `stores`.id FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} ORDER BY `stores`.`Name` ASC")) !== false){
	while($row = $storeQuery->fetch_assoc()){
		$stores[$row['Name']] = $row['id'];
	}
}

$maxAccounts = max($maxAccounts, $numAccounts);
?>
<div class='form-account'>
	<span class="form-header"><?php echo sprintf(__("You are using %s out of your %s available logins."), '<span class="large">'.$numAccounts.'</span>', '<span class="large">'.$maxAccounts.'</span>'); ?></span><br />
	
	<table class='table table-white table-bordered table-hover table-data'>
		<thead>
			<th><?php _e("Email Address"); ?></th>
			<th><?php _e("Store"); ?></th>
			<th><?php _e("Permissions"); ?></th>
		</thead>
		<tbody>
			<?php
				if($query !== false && $query->num_rows > 0){
					while($row = $query->fetch_assoc()){
						$email = $row['EmailAddress'];
						$store = empty($row['StoreName']) ? '' : $row['StoreName'];
						$storeID = $row['StoreID'];
						$accountID = $row['AccountID'];
						
						$permissions = @intval($row['Permissions']);
						$permissionsText = __(Permissions::translateH($permissions));
						
						$css = $accountID == $_SESSION['AccountID'] ? " class='highlight'" : '';
			?>
						<tr<?php echo $css; ?> data-id='<?php echo $accountID; ?>'>
							<td><?php echo $email; ?></td>
							<td>
							<?php
								if($permissions == Permissions::MODIFY_COMPANY){
									echo 'N/A';
								} else {
							?>
								<select class='ddstores'>
								<?php
									echo "<option value=''>All Stores</option>";
									foreach($stores as $storeName => $sID){
										$selected = '';
										if(!empty($storeID) && $storeID == $sID){ $selected = ' selected'; }
										echo "<option value='{$storeID}'{$selected}>{$storeName}</option>";
									}
								?>
								</select>
							<?php } ?>
							</td>
							<td><?php echo $permissionsText; ?></td>
						</tr>
			<?php
					}
				}
			?>
		</tbody>
	</table>
	
	<?php if($numAccounts < $maxAccounts){ ?>
	<span class="form-header"><?php echo sprintf(__("You can make an additional %s logins using the form below. If you would like more than %s logins, you can purchase additional logins via the Upgrade tab to the left."), '<span class="large">'.($maxAccounts-$numAccounts).'</span>', '<span class="large">'.($maxAccounts-$numAccounts).'</span>'); ?></span><br />
	<form id='frmAccount' action='/settings/save_login.php' method='post'>
		<input class='in' name='txtEmailAddress' placeholder='<?php _e("Email Address"); ?>' /><br /><br />
		<span class='form-header'><?php _e("What store should this account have access to?"); ?></span>
		<select class='in' name='ddStores' id='ddStores'>
		<?php			
			echo "<option value=''>".__("All Stores")."</option>";
			foreach($stores as $storeName => $storeID){
				$selected = '';
				echo "<option value='{$storeID}'{$selected}>{$storeName}</option>";
			}
		?>
		</select>
		<div class="submit-next"><?php _e('Create Login'); ?></div>
	</form>
	<?php } else { ?>
	<span class="form-header"><?php _e("If you would like to add additional logins, please send us an email or give us a call."); ?></span>
	<?php } ?>

</div>

<script type='text/javascript'>
$('tr > td > select.ddstores').change(function(){
	var $self = $(this);
	var sid = $self.parent().parent().data('id');
	if(typeof sid !== 'undefined'){
		$.post('/settings/save_login.php', {'id' : sid, 'store' : $self.val()}, function(data){
			if(data != 'Invalid'){
				$self.parent().parent().children('td:eq(2)').text(data);
			}
		});
	}
});
</script>