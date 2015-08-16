<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

$message = '';

if(isset($_POST)){
	$password = Brevada::FromPOST('txtPassword');
	$password2 = Brevada::FromPOST('txtPassword2');
	if(!empty($password) && !empty($password2) && $password == $password2){
		if(strlen($password) < 8){
			$message = __('Your password is too short. It must be at least 8 characters.');
		} else {
			$password = Brevada::HashPassword($password);
			if(($stmt = Database::prepare("UPDATE `accounts` SET `Password` = ? WHERE `id` = ? LIMIT 1")) !== false){
				$accountID = $_SESSION['AccountID'];
				$stmt->bind_param('si', $password, $accountID);
				if($stmt->execute()){
					$message = __('Your password has been changed.');
				} else {
					$message = __('There was an error changing your password.');
				}
				$stmt->close();
			}
		}
	}
}
?>
<form id='frmAccount' action='settings?section=account' method='post'>
<?php if(!empty($message)){ echo "<p class='message'>{$message}</p>"; } ?>
<div class='form-account'>
	<span class="form-header"><?php _e('Change Your Password'); ?></span>
	<span class="form-subheader"><?php _e('A strong password contains uppercase, lowercase, numbers and symbols (!@#$).'); ?></span>
	
	<input class='in' type='password' name='txtPassword' placeholder='<?php _e("Enter your new password."); ?>' />
	<input class='in' type='password' name='txtPassword2' placeholder='<?php _e("Re-enter your new password."); ?>' />
	
	<div id="submit" class="submit-next"><?php _e('Save'); ?></div>
</div>
</form>