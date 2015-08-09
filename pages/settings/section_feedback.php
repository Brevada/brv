<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');
$_GET = $this->getParameter('GET');

$message = '';

if((!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))) || (!empty($_POST['ddStores']) && $_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES))){
	
	$store_id = '';
	if(empty($_POST['ddStores'])){	
		$store_id = Brevada::validate($_SESSION['StoreID'], VALIDATE_DATABASE);
	} else {
		$store_id = Brevada::validate(Brevada::FromPOST('ddStores'), VALIDATE_DATABASE);
		$check = Database::query("SELECT `stores`.`id` FROM `stores` WHERE `stores`.`CompanyID` = {$_SESSION['CompanyID']} AND `stores`.`id` = {$store_id} LIMIT 1");
		if($check === false || $check->num_rows == 0 || !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
			unset($_POST);
			Brevada::Redirect('/settings?section=feedback');
		}
	}

	if(isset($_POST) && isset($_POST['posts-token'])){
		$posts_tokens = explode(',', $_POST['posts-token']);
		if($posts_tokens !== false){

			if(($query = Database::query("SELECT aspects.ID FROM aspects WHERE aspects.StoreID = {$store_id} AND aspects.`Active` = 1")) !== false){
				while($row = $query->fetch_assoc()){
					if(!in_array($row['ID'], $posts_tokens)){
						Database::query("UPDATE aspects SET aspects.`Active` = 0 WHERE aspects.StoreID = {$store_id} AND aspects.ID = {$row['ID']} LIMIT 1");
					}
				}
			}
		
			foreach($posts_tokens as $token){
				$token = Brevada::validate($token, VALIDATE_DATABASE);
				if(!empty($token)){
					if(($query = Database::query("SELECT aspects.`Active` FROM aspects WHERE aspects.StoreID = {$store_id} AND aspects.AspectTypeID = {$token} LIMIT 1")) !== false){
						if($query->num_rows > 0){
							Database::query("UPDATE aspects SET aspects.`Active` = 1 WHERE aspects.StoreID = {$store_id} AND aspects.AspectTypeID = {$token} LIMIT 1");
						} else {
							if(($stmt = Database::prepare("INSERT INTO aspects (`StoreID`, `AspectTypeID`) SELECT stores.id, (SELECT aspect_type.ID FROM aspect_type WHERE aspect_type.ID = ?) as AspectTypeID FROM stores WHERE stores.id = ?")) !== false){
								$stmt->bind_param('ii', $token, $store_id);
								$stmt->execute();
								$stmt->close();
							}
						}
					}
				}
			}
		}
		
		if($_SESSION['Corporate'] && !empty($_POST['ddStores'])){
			unset($_POST);
			Brevada::Redirect('/settings?section=feedback&saved=1');
		} else if(!empty($_SESSION['StoreID']) && $_SESSION['Corporate']){
			Brevada::Redirect('/dashboard?s='.$_SESSION['StoreID']);
		} else {
			Brevada::Redirect('/dashboard');
		}
	}	
}
?>
<form id='frmAccount' action='settings?section=feedback' method='post'>
<div class='form-account'>
	<?php if(!empty($message)){ echo "<p class='message'>{$message}</p>"; } ?>
	<?php if(!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){ ?>
	<span class="form-header"><?php _e('What do you want to get feedback on?'); ?></span>
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