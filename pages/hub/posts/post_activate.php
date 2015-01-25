<?php
$id = @intval(Brevada::validate($_GET['id']));
$yes = strtolower(Brevada::validate($_GET['yes'], VALIDATE_DATABASE));

if(Brevada::IsLoggedIn()){
	$uid = $_SESSION['user_id'];
	if($yes == 'yes' || $yes == 'no'){
		Database::query("UPDATE posts SET active='{$yes}' WHERE id='{$id}' AND `user_id` = '{$uid}' LIMIT 1");
	}
}

Brevada::Redirect('/hub/hub.php');
?>