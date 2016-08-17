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

$welcome_message = '';
$allow_comments = false;

$lookup_result = false;
if (($stmt = Database::prepare("
	SELECT WelcomeMessage, AllowComments FROM store_features
	JOIN stores ON stores.FeaturesID = store_features.id
	WHERE stores.id = ?
")) !== false){
	$stmt->bind_param('i', $store_id);
	if(!$stmt->execute()){
		$message = "Unknown error. 500.";
	} else {
		$stmt->bind_result($welcome_message, $allow_comments);
		if($lookup_result = $stmt->fetch()){
			$welcome_message = empty($welcome_message) ? '' : $welcome_message;
			$allow_comments = $allow_comments == 1;
		}
	}
	$stmt->close();
}

if (is_null($lookup_result)){
	$features_id = -1;
	if (($stmt = Database::prepare("
		INSERT INTO store_features (CollectionTemplate) VALUES (NULL)
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
	$welcome_message = trim(strip_tags(Brevada::FromPOST('txtMessage')));
	$allow = isset($_POST['chkAllowComments']) ? 1 : 0;
	$allow_comments = $allow == 1;
	
	if (($stmt = Database::prepare("
		UPDATE stores
		JOIN store_features ON store_features.id = stores.FeaturesID
		SET WelcomeMessage = ?, AllowComments = ? 
		WHERE stores.id = ?
	")) !== false){
		$stmt->bind_param('sii', $welcome_message, $allow, $store_id);
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
		<textarea class='form-control' name='txtMessage' rows="3" placeholder="<?php _e("Give %store% Feedback"); ?>"><?= $welcome_message; ?></textarea>
	</div>
	<br />
	<div class='form-group'>
		<span class="form-header"><?php _e("Allow Comments");?></span>
		<span class="form-subheader"><?php _e("If checked, customers will be able to send you comments viewable in your dashboard."); ?></span>
		<input type='checkbox' name='chkAllowComments' <?= $allow_comments ? 'checked' : ''; ?>>&nbsp;<span>Allow Comments</span>
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
		<textarea class='form-control' name='txtMessage' rows="3" placeholder="<?php _e("Give %store% Feedback"); ?>"><?= $welcome_message; ?></textarea>
	</div>
	<br />
	<div class='form-group'>
		<span class="form-header"><?php _e("Allow Comments");?></span>
		<span class="form-subheader"><?php _e("If checked, customers will be able to send you comments viewable in your dashboard."); ?></span>
		<input type='checkbox' name='chkAllowComments' <?= $allow_comments ? 'checked' : ''; ?>>&nbsp;<span>Allow Comments</span>
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