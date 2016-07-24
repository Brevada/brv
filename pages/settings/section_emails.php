<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');
$_GET = $this->getParameter('GET');

$message = '';

$store_id = false;

if ($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	if(!empty($_POST['ddStores'])){
		$store_id = Brevada::validate(@intval(Brevada::FromPOST('ddStores')), VALIDATE_DATABASE);
	} else {
		$store_id = Brevada::validate(@intval(Brevada::FromGET('store')), VALIDATE_DATABASE);
	}
	if(!empty($store_id)){
		$check = Database::query("SELECT `stores`.`id` FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} AND `stores`.`id` = {$store_id} LIMIT 1");
		if($check === false || $check->num_rows == 0){
			$store_id = false;
		}	
	}
} else if(!$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE)) {
	$store_id = $_SESSION['StoreID'];
}

$col_template = false;
$col_location = 0;

if ($store_id !== false && ($stmt = Database::prepare("
		SELECT `CollectionTemplate`, `CollectionLocation`
		FROM stores
		JOIN store_features ON store_features.id = stores.FeaturesID
		WHERE stores.id = ?
	")) !== false){
	$stmt->bind_param('i', $store_id);
	if ($stmt->execute()){
		$stmt->store_result();
		if($stmt->num_rows > 0){
			$stmt->bind_result($col_template, $col_location);
			$stmt->fetch();
			$col_template = DataTemplate::fromJSON($col_template);
			$stmt->close();
		} else {
			$stmt->close();
			
			// Doesn't exist. Create and link.
			
			$features_id = -1;
			if (($stmt = Database::prepare("
				INSERT INTO store_features (CollectionTemplate, CollectionLocation) VALUES (NULL, NULL)
			")) !== false){
				if($stmt->execute()){
					$stmt->store_result();
					$features_id = Database::getCon()->insert_id;
				}
				$stmt->close();
			}
			
			if ($features_id > 0) {
				if (($stmt = Database::prepare("
					UPDATE stores SET FeaturesID = ? WHERE stores.id = ?
				")) !== false){
					$stmt->bind_param('ii', $features_id, $store_id);
					if(!$stmt->execute()){
						$message = "Unknown error. 500.";
					}
					$stmt->close();
				}
			}
		}
	}
}

if(isset($_POST) && isset($_POST['rdDisplay'])){
	$col_location = @intval($_POST['rdDisplay']) % 5;
	$message = trim(strip_tags(Brevada::FromPOST('txtMessage')));
	
	$col_template = new DataTemplate('postpre/email');
	$col_template->set('message', $message);
	
	$template_string = $col_template->toJSON();
	
	if (($stmt = Database::prepare("
		UPDATE stores
		JOIN store_features ON store_features.id = stores.FeaturesID
		SET CollectionTemplate = ?, CollectionLocation = ? 
		WHERE stores.id = ?
	")) !== false){
		$stmt->bind_param('sii', $template_string, $col_location, $store_id);
		if(!$stmt->execute()){
			$message = "Unknown error. 500.";
		} else {
			$message = "Save changes.";
		}
		$stmt->close();
	}
}
?>
<form id='frmAccount' action='settings?section=emails' method='post'>
<div class='form-account'>
	<?php if(!empty($message)){ echo "<p class='message'>{$message}</p>"; } ?>
	<?php if(!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){ ?>
	<span class="form-header"><?php _e('Settings'); ?></span>
	<span class="form-subheader"><?php _e("Optionally request that your customers provide their email address. Their email address will appear on the dashboard's 'Customer' page next to their response."); ?></span>
	<div class='form-group'>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='3' <?= $col_location == 3 ? 'checked' : ''; ?> /> <?= __("Always prompt for customer's email before survey."); ?></label>
		</div>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='2' <?= $col_location == 2 ? 'checked' : ''; ?> /> <?= __("Always prompt for customer's email after survey."); ?></label>
		</div>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='1' <?= $col_location == 1 ? 'checked' : ''; ?> /> <?= __("Prompt for customer's email after survey if response score was poor."); ?></label>
		</div>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='0' <?= $col_location == 0 ? 'checked' : ''; ?> /> <?= __("Never prompt for customer's email."); ?></label>
		</div>
	</div>
	<div class='form-group'>
		<span class="form-header"><?php _e("Custom Message");?></span>
		<span class="form-subheader"><?php _e("This message will appear alongside the email form."); ?></span>
		<textarea class='form-control' name='txtMessage' rows="3" placeholder="<?php _e("Please provide us with your email address so that we can be sure to properly remedy any negative situations."); ?>"><?= $col_template !== false ? $col_template->get('message') : ''; ?></textarea>
	</div>
	
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
	<?php } else if($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) { ?>
	<span class="form-header"><?php _e('Settings'); ?></span>
	<span class="form-subheader"><?php _e("Optionally request that your customers provide their email address. Their email address will appear on the dashboard's 'Customer' page next to their response."); ?></span>
	<span class="form-subheader"><?php _e("These options are specific to the selected store."); ?></span>
	<select class='in' name='ddStores' id='ddStores'>
	<?php	
	if(($query = Database::query("SELECT `stores`.`Name`, `stores`.id FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} ORDER BY `stores`.`Name` ASC")) !== false){
		echo "<option value=''>Select a store...</option>";
		while($row = $query->fetch_assoc()){
			$selected = '';
			if(!empty($store_id) && $store_id == $row['id']){ $selected = ' selected'; }
			echo "<option value='{$row['id']}'{$selected}>{$row['Name']}</option>";
		}
	}
	?>
	</select>
	<br /><br />
	<?php if(!empty($store_id) && @intval($store_id) > 0){ ?>
	<div class='form-group'>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='3' <?= $col_location == 3 ? 'checked' : ''; ?> /> <?= __("Always prompt for customer's email before survey."); ?></label>
		</div>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='2' <?= $col_location == 2 ? 'checked' : ''; ?> /> <?= __("Always prompt for customer's email after survey."); ?></label>
		</div>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='1' <?= $col_location == 1 ? 'checked' : ''; ?> /> <?= __("Prompt for customer's email after survey if response score was poor."); ?></label>
		</div>
		<div class='radio'>
			<label><input type='radio' name='rdDisplay' value='0' <?= $col_location == 0 ? 'checked' : ''; ?> /> <?= __("Never prompt for customer's email."); ?></label>
		</div>
	</div>
	<div class='form-group'>
		<span class="form-header"><?php _e("Custom Message");?></span>
		<span class="form-subheader"><?php _e("This message will appear alongside the email form."); ?></span>
		<textarea class='form-control' name='txtMessage' rows="3" placeholder="<?php _e("Please provide us with your email address so that we can be sure to properly remedy any negative situations."); ?>"><?= $col_template !== false ? $col_template->get('message') : ''; ?></textarea>
	</div>
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
	<?php } ?>
	<?php } ?>
</div>
</form>

<script type='text/javascript'>
$('#ddStores').change(function(){
	window.location = '/settings?section=emails&store='+$(this).val();
});
</script>