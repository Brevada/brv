<?php
$id = @intval($_GET['id']);

if(Brevada::IsLoggedIn()){
	$uid = $_SESSON['user_id'];
	$res = Database::query("DELETE FROM `messages` WHERE `id`='{$id}' AND `user_id` = '{$uid}'");
}

Brevada::Redirect('/hub/hub.php');
?>