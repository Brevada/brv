<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');
$_GET = $this->getParameter('GET');

$message = '';

$session_check = 1;

// TODO: These permission checks need to change.. can't retrieve values in a MODIFY check.
if((!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))) || (!empty($_POST['ddStores']) && $_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES))){
	
	$company_id = Brevada::validate($_SESSION['CompanyID'], VALIDATE_DATABASE);
	
	$store_id = '';
	if(empty($_POST['ddStores'])){	
		$store_id = Brevada::validate($_SESSION['StoreID'], VALIDATE_DATABASE);
	} else {
		$store_id = Brevada::validate(Brevada::FromPOST('ddStores'), VALIDATE_DATABASE);
		$check = Database::query("SELECT `stores`.`id` FROM `stores` WHERE `stores`.`CompanyID` = {$company_id} AND `stores`.`id` = {$store_id} LIMIT 1");
		if($check === false || $check->num_rows == 0 || !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
			unset($_POST);
			Brevada::Redirect('/settings?section=feedback');
		}
	}

	if(isset($_POST) && isset($_POST['posts-token'])){
		$posts_tokens = explode(',', $_POST['posts-token']);
	}
	
	if(isset($_POST) && !empty($_POST['txtCustomAspect'])){
		$custom = trim(strip_tags($_POST['txtCustomAspect']));
		if(!empty($custom)){			
			// If title exists, don't allow.
			if(($stmt = Database::prepare("SELECT FROM `aspect_type` WHERE `Title` = ? AND (`CompanyID` = ? OR `CompanyID` IS NULL) LIMIT 1")) !== false){
				$stmt->bind_param('si', $custom, $company_id);
				if($stmt->execute()){
					$stmt->store_result();
					if($stmt->num_rows > 0){
						$message = __("An aspect with this name already exists.");
					}
				}
				$stmt->close();
			}
			
			if(empty($message) && ($stmt = Database::prepare("INSERT INTO `aspect_type` (`Title`, `CompanyID`) VALUES (?, ?)")) !== false){
				$stmt->bind_param('si', $custom, $company_id);
				if($stmt->execute()){
					$message = "Custom aspect created.";
					$posts_tokens[] = Database::getCon()->insert_id;
				} else {
					$message = "Failed to create custom aspect.";
				}
			}
		}
	}
	
	if(isset($_POST) && isset($_POST['posts-token'])){
		if($posts_tokens !== false){

			$update = false;
		
			if(($query = Database::query("SELECT aspects.ID FROM aspects WHERE aspects.StoreID = {$store_id} AND aspects.`Active` = 1")) !== false){
				while($row = $query->fetch_assoc()){
					if(!in_array($row['ID'], $posts_tokens)){
						$update = true;
						Database::query("UPDATE aspects SET aspects.`Active` = 0 WHERE aspects.StoreID = {$store_id} AND aspects.ID = {$row['ID']} LIMIT 1");
					}
				}
			}
		
			foreach($posts_tokens as $token){
				$token = Brevada::validate($token, VALIDATE_DATABASE);
				if(!empty($token)){
					if(($query = Database::query("
						SELECT aspects.`Active` FROM aspects
						JOIN aspect_type ON aspect_type.id = aspects.AspectTypeID
						WHERE 
							aspects.StoreID = {$store_id} AND 
							aspects.AspectTypeID = {$token} AND
							(aspect_type.CompanyID IS NULL OR aspect_type.CompanyID = {$company_id})
						LIMIT 1
						")) !== false){
						if($query->num_rows > 0){
							Database::query("UPDATE aspects SET aspects.`Active` = 1 WHERE aspects.StoreID = {$store_id} AND aspects.AspectTypeID = {$token} LIMIT 1");
						} else {
							if(($stmt = Database::prepare("INSERT INTO aspects (`StoreID`, `AspectTypeID`) SELECT stores.id, (SELECT aspect_type.ID FROM aspect_type WHERE aspect_type.ID = ?) as AspectTypeID FROM stores WHERE stores.id = ?")) !== false){
								$stmt->bind_param('ii', $token, $store_id);
								if($stmt->execute() && $stmt->num_rows > 0){
									$update = true;
								}
								$stmt->close();
							}
						}
					}
				}
			}
			
			if ($update){
				Tablet::RestartByStore($_SESSION['StoreID']);
				if (empty($message)){
					$message = "Changes saved. Tablets will be restarted.";
				}
			}
		}
	}
	
	if ($store_id !== false && ($stmt = Database::prepare("
			SELECT `SessionCheck`
			FROM stores
			JOIN store_features ON store_features.id = stores.FeaturesID
			WHERE stores.id = ?
		")) !== false){
		$stmt->bind_param('i', $store_id);
		if ($stmt->execute()){
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$stmt->bind_result($session_check);
				$stmt->fetch();
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
	
	if(isset($_POST)){
		$session_check = isset($_POST['chkSessionCheck']) ? 1 : 0;
		
		if (($stmt = Database::prepare("
			UPDATE stores
			JOIN store_features ON store_features.id = stores.FeaturesID
			SET SessionCheck = ? 
			WHERE stores.id = ?
		")) !== false){
			$stmt->bind_param('ii', $session_check, $store_id);
			if(!$stmt->execute()){
				$message = "Unknown error. 500.";
			}
			$stmt->close();
		}
	}
	
}
?>
<form id='frmAccount' action='settings?section=feedback' method='post'>
<div class='form-account'>
	<?php if(!empty($message)){ echo "<p class='message'>{$message}</p>"; } ?>
	<?php if(!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){ ?>
	<span class="form-header"><?php _e('What do you want to get feedback on?'); ?><span class='pull-right'><i class='fa fa-info-circle help' data-tooltip="<?php _e("You can toggle aspects on and off without worrying about losing data."); ?>"></i></span></span>
	<div class='token-container'>
		<div class='tokens'>
			<?php
			if ($stmt = Database::prepare("
				SELECT aspect_type.Title, aspect_type.ID as AspectTypeID,
				(SELECT aspects.id FROM `aspects` WHERE `aspects`.AspectTypeID = aspect_type.ID AND `aspects`.Active = 1 AND `aspects`.StoreID = ?) as `Selected`
				FROM aspect_type
				WHERE `CompanyID` IS NULL
				ORDER BY aspect_type.Title ASC
			")) {
				$stmt->bind_param('i', $store_id);
				if ($stmt->execute()){
					$stmt->store_result();
					$stmt->bind_result($aspect_title, $aspect_id, $aspect_selected);
					while ($stmt->fetch()){
						echo "<div class='token".(empty($aspect_selected) ? ' noselect' : ' selected')."' data-tokenid='{$aspect_id}'><span>".__($aspect_title)."</span></div>";
					}
				}
			}
			?>
			<input type='hidden' name='posts-token' id='tokens' />
		</div>
	</div>
	<br /><br />
	<span class="form-header"><?php _e('Custom aspects:'); ?><span class='pull-right'><i class='fa fa-info-circle help' data-tooltip="<?php _e("Custom aspects are unique to your company and can contain branding and store-specific information."); ?>"></i></span></span>
	<?php $no_custom = true; ?>
	<div class='token-container'>
		<div class='tokens'>
			<?php
			if ($stmt = Database::prepare("
				SELECT aspect_type.Title, aspect_type.ID as AspectTypeID,
				(SELECT aspects.id FROM `aspects` WHERE `aspects`.AspectTypeID = aspect_type.ID AND `aspects`.Active = 1 AND `aspects`.StoreID = ?) as `Selected`
				FROM aspect_type
				WHERE `CompanyID` = ?
				ORDER BY aspect_type.Title ASC
			")) {
				$company_id = $_SESSION['CompanyID'];
				$stmt->bind_param('ii', $store_id, $company_id);
				if ($stmt->execute()){
					$stmt->store_result();
					$stmt->bind_result($aspect_title, $aspect_id, $aspect_selected);
					while ($stmt->fetch()){
						$no_custom = false;
						echo "<div class='token".(empty($aspect_selected) ? ' noselect' : ' selected')."' data-tokenid='{$aspect_id}'><span>".__($aspect_title)."</span></div>";
					}
				}
			}
			?>
		</div>
	</div>
	<?php if ($no_custom){ ?>
	<div class='well well-sm'><?php _e("You currently have no custom aspects."); ?></div>
	<?php } ?>
	<br />
	
	<div class='form-group'>
		<span class="form-header"><?php _e("Create a new custom aspect:");?><span class='pull-right'><i class='fa fa-info-circle help' data-tooltip="<?php _e("For example, if you have a signature dish, you can name it.<br/><br/>Keep in mind, the aspect name is case-sensitive."); ?>"></i></span></span>
		<input type='text' class='form-control' name='txtCustomAspect' placeholder="<?php _e("e.g. Signature Dish"); ?>" />
	</div>
	<br />
	<div class='form-group'>
		<span class="form-header"><?php _e("Session checking:");?></span>
		<span class="form-subheader"><input type='checkbox' name='chkSessionCheck' <?= $session_check ? 'checked' : ''; ?> />&nbsp;<?php _e("If enabled, users must wait between feedback sessions. Disable this if you are using your own device to gather feedback.");?></span>
	</div>
	
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
	<?php } else if($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) { ?>
	<span class="form-header"><?php _e('What do you want to get feedback on?'); ?></span>
	<select class='in' name='ddStores' id='ddStores'>
	<?php
	$store_id = Brevada::validate(@intval(Brevada::FromGET('store')), VALIDATE_DATABASE);
	$check = Database::query("SELECT `stores`.`id` FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} AND `stores`.`id` = {$store_id} LIMIT 1");
	if($check === false || $check->num_rows == 0){
		$store_id = '';
	}
	
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
	<?php if(!empty($store_id) && @intval($store_id) > 0){ ?>
	<br /><br />
	<div class='token-container'>
		<div class='tokens'>
			<?php
			if(($query = Database::query("SELECT aspect_type.Title, aspect_type.ID as AspectTypeID, (SELECT aspects.id FROM `aspects` WHERE `aspects`.AspectTypeID = aspect_type.ID AND `aspects`.Active = 1 AND  `aspects`.StoreID = {$store_id}) as `Selected` FROM aspect_type ORDER BY aspect_type.Title ASC")) !== false){
				while($row = $query->fetch_assoc()){
					echo "<div class='token".(empty($row['Selected']) ? ' noselect' : ' selected')."' data-tokenid='{$row['AspectTypeID']}'><span>".__($row['Title'])."</span></div>";
				}
			}
			?>
			<input type='hidden' name='posts-token' id='tokens' />
		</div>
	</div>
	
	<br /><br />
	<span class="form-header"><?php _e('Custom aspects:'); ?><span class='pull-right'><i class='fa fa-info-circle help' data-tooltip="<?php _e("Custom aspects are unique to your company and can contain branding and store-specific information."); ?>"></i></span></span>
	<?php $no_custom = true; ?>
	<div class='token-container'>
		<div class='tokens'>
			<?php
			if ($stmt = Database::prepare("
				SELECT aspect_type.Title, aspect_type.ID as AspectTypeID,
				(SELECT aspects.id FROM `aspects` WHERE `aspects`.AspectTypeID = aspect_type.ID AND `aspects`.Active = 1 AND `aspects`.StoreID = ?) as `Selected`
				FROM aspect_type
				WHERE `CompanyID` = ?
				ORDER BY aspect_type.Title ASC
			")) {
				$company_id = $_SESSION['CompanyID'];
				$stmt->bind_param('ii', $store_id, $company_id);
				if ($stmt->execute()){
					$stmt->store_result();
					$stmt->bind_result($aspect_title, $aspect_id, $aspect_selected);
					while ($stmt->fetch()){
						$no_custom = false;
						echo "<div class='token".(empty($aspect_selected) ? ' noselect' : ' selected')."' data-tokenid='{$aspect_id}'><span>".__($aspect_title)."</span></div>";
					}
				}
			}
			?>
		</div>
	</div>
	<?php if ($no_custom){ ?>
	<div class='well well-sm'><?php _e("You currently have no custom aspects."); ?></div>
	<?php } ?>
	<br />
	<div class='form-group'>
		<span class="form-header"><?php _e("Create a new custom aspect:");?><span class='pull-right'><i class='fa fa-info-circle help' data-tooltip="<?php _e("For example, if you have a signature dish, you can name it.<br/><br/>Keep in mind, the aspect name is case-sensitive."); ?>"></i></span></span>
		<input type='text' class='form-control' name='txtCustomAspect' placeholder="<?php _e("e.g. Signature Dish"); ?>" />
	</div>
	
	<br />
	<div class='form-group'>
		<span class="form-header"><?php _e("Session checking:");?></span>
		<span class="form-subheader"><input type='checkbox' name='chkSessionCheck' <?= $session_check ? 'checked' : ''; ?> />&nbsp;<?php _e("If enabled, users must wait between feedback sessions. Disable this if you are using your own device to gather feedback.");?></span>
	</div>
	
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
	<?php } ?>
	<?php } ?>
</div>
</form>

<script type='text/javascript'>
$('#ddStores').change(function(){
	window.location = '/settings?section=feedback&store='+$(this).val();
});
</script>