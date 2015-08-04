<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

if(!empty($_SESSION['StoreID']) && (($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || !$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){
	$store_id = Brevada::validate($_SESSION['StoreID'], VALIDATE_DATABASE);

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
		
		if(!empty($_SESSION['StoreID']) && $_SESSION['Corporate']){
			Brevada::Redirect('/dashboard?s='.$_SESSION['StoreID']);
		} else {
			Brevada::Redirect('/dashboard');
		}
	}	
}
?>
<form id='frmAccount' action='settings?section=feedback' method='post'>
<div class='form-account'>
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
	<?php } ?>
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
</div>
</form>