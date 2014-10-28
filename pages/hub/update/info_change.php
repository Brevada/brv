<?php
$name = Brevada::validate(empty($_POST['name']) ? '' : $_POST['name'], VALIDATE_DATABASE);
$email = Brevada::validate(empty($_POST['email']) ? '' : $_POST['email'], VALIDATE_DATABASE);
$type = Brevada::validate(empty($_POST['type']) ? '' : $_POST['type'], VALIDATE_DATABASE);
$user_id = $_SESSION['user_id'];

if(!empty($name) && !empty($email)){
	if(empty($type)){
		Database::query("UPDATE users SET email='{$email}', name='{$name}' WHERE id='{$user_id}'");
	} else {
		Database::query("UPDATE users SET email='{$email}', name='{$name}', type='{$type}' WHERE id='{$user_id}'");
	}
}

Brevada::Redirect('/hub/hub.php');
?>