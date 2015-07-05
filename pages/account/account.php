<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/account.css');
$this->addResource('/js/account.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/logout');
}

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

if(isset($_POST) && isset($_POST['posts-token'])){
	$posts_tokens = explode(',', $_POST['posts-token']);
	if($posts_tokens !== false){

		if(($query = Database::query("SELECT aspects.ID FROM aspects WHERE aspects.OwnerID = {$user_id} AND aspects.`Active` = 1")) !== false){
			while($row = $query->fetch_assoc()){
				if(!in_array($row['ID'], $posts_tokens)){
					Database::query("UPDATE aspects SET aspects.`Active` = 0 WHERE aspects.OwnerID = {$user_id} AND aspects.ID = {$row['ID']} LIMIT 1");
				}
			}
		}
	
		foreach($posts_tokens as $token){
			$token = Brevada::validate($token, VALIDATE_DATABASE);
			if(!empty($token)){
				if(($query = Database::query("SELECT aspects.`Active` FROM aspects WHERE aspects.OwnerID = {$user_id} AND aspects.AspectTypeID = {$token} LIMIT 1")) !== false){
					if($query->num_rows > 0){
						Database::query("UPDATE aspects SET aspects.`Active` = 1 WHERE aspects.OwnerID = {$user_id} AND aspects.AspectTypeID = {$token} LIMIT 1");
					} else {
						if(($stmt = Database::prepare("INSERT INTO aspects (`OwnerID`, `AspectTypeID`) SELECT users.id, (SELECT aspect_type.ID FROM aspect_type WHERE aspect_type.ID = ?) as AspectTypeID FROM users WHERE users.id = ?")) !== false){
							$stmt->bind_param('ii', $token, $user_id);
							$stmt->execute();
							$stmt->close();
						}
					}
				}
			}
		}
	}
	Brevada::Redirect('/dashboard');
}
?>
<div class='top-banner'>
	<img class='logo-quote link' src='/images/quote.png' data-link='' />
	<div class='dropdown-menu noselect'>
		<div class='three-lines'>
			<i class='fa fa-ellipsis-h'></i>
		</div>
		<ul>
			<li class='link' data-link='dashboard'><?php _e('Dashboard'); ?></li>
			<li class='link' data-link='logout'><?php _e('Logout'); ?></li>
		</ul>
	</div>
</div>

<div class='spacer'></div>

<form id='frmAccount' action='account' method='post'>
<div class='form-account'>
	<span class="form-header"><?php _e('What do you want to get feedback on?'); ?></span>
	<div class='token-container'>
		<div class='tokens'>
			<?php
			if(($query = Database::query("SELECT aspect_type.Title, aspect_type.ID as AspectTypeID, (SELECT aspects.id FROM `aspects` WHERE `aspects`.AspectTypeID = aspect_type.ID AND `aspects`.Active = 1 AND  `aspects`.OwnerID = {$user_id}) as `Selected` FROM aspect_type ORDER BY aspect_type.Title ASC")) !== false){
				while($row = $query->fetch_assoc()){
					echo "<div class='token".(empty($row['Selected']) ? ' noselect' : ' selected')."' data-tokenid='{$row['AspectTypeID']}'><span>".__($row['Title'])."</span></div>";
				}
			}
			?>
			<input type='hidden' name='posts-token' id='tokens' />
		</div>
	</div>
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
</div>
</form>