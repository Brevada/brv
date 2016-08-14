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

$dataT = DataTemplate::fromStore($store_id);
if ($dataT !== false && isset($dataT['tpl'])){
	$col_template = $dataT['tpl'];
	$col_location = $dataT['loc'];
} else {
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

if(isset($_POST) && isset($_POST['txtMessage'])){
	$message = trim(strip_tags(Brevada::FromPOST('txtMessage')));
	
	$col_template = DataTemplate::fromStore($store_id);
	$col_template = $col_template['tpl'];
	$col_template->setWelcome($message);
	
	$template_string = $col_template->toJSON();
	
	if (($stmt = Database::prepare("
		UPDATE stores
		JOIN store_features ON store_features.id = stores.FeaturesID
		SET CollectionTemplate = ? 
		WHERE stores.id = ?
	")) !== false){
		$stmt->bind_param('si', $template_string, $store_id);
		if(!$stmt->execute()){
			$message = "Unknown error. 500.";
		} else {
			$message = "Changes saved. Tablets will restart.";
			Tablet::RestartByStore($store_id);
		}
		$stmt->close();
	}
}
?>
<form id='frmAccount' action='settings?section=personalize' method='post'>
<div class='form-account'>
	<?php if(!empty($message)){ echo "<p class='message'>{$message}</p>"; } ?>
	<?php if(!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){ ?>
	
	<div class='form-group'>
		<span class="form-header"><?php _e("Custom Welcome Message");?></span>
		<span class="form-subheader"><?php _e("Set a custom welcome message to greet your customers or offer them an incentive. You can write %store% in place of your store's name (or manually enter what you wish)."); ?></span>
		<br />
		<textarea class='form-control' name='txtMessage' rows="3" placeholder="<?php _e("Give %store% Feedback"); ?>"><?= $col_template !== false ? $col_template->getWelcome() : ''; ?></textarea>
	</div>
	
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
	<?php } else if($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) { ?>
	<span class="form-header"><?php _e('Settings'); ?></span>
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
		<span class="form-header"><?php _e("Custom Welcome Message");?></span>
		<span class="form-subheader"><?php _e("Set a custom welcome message to greet your customers or offer them an incentive. You can write %store% in place of your store's name (or manually enter what you wish)."); ?></span>
		<br />
		<textarea class='form-control' name='txtMessage' rows="3" placeholder="<?php _e("Give %store% Feedback"); ?>"><?= $col_template !== false ? $col_template->getWelcome() : ''; ?></textarea>
	</div>
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
	<?php } ?>
	<?php } ?>
</div>
</form>

<script type='text/javascript'>
$('#ddStores').change(function(){
	window.location = '/settings?section=personalize&store='+$(this).val();
});
</script>