<?php
$id = @intval(Brevada::validate($_GET['id']));
$yes = strtolower(Brevada::validate($_GET['yes'], VALIDATE_DATABASE));

if($yes == 'yes' || $yes == 'no'){
	Database::query("UPDATE posts SET active='{$yes}' WHERE id='{$id}' LIMIT 1");
}

Brevada::Redirect('/hub/hub.php');
?>