<?php
$pass = Brevada::validate($_POST['pass'], VALIDATE_DATABASE);
$user_id = $_SESSION['user_id'];

if(!empty($pass)){
	Database::query("UPDATE users SET password='{$pass}' WHERE id='{$user_id}'");
}

//perhaps user should be logged out to test the new password?
Brevada::Redirect('/hub/hub.php');
?>