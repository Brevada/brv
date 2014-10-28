<?php
$name = Brevada::validate($_POST['name'], VALIDATE_DATABASE);
$description = Brevada::validate($_POST['description'], VALIDATE_DATABASE);
$post_id = Brevada::validate($_POST['post_id'], VALIDATE_DATABASE);

if(!empty($name) && !empty($description) && $post_id!=''){
	Database::query("UPDATE posts SET description='{$description}', `name`='{$name}' WHERE id='{$post_id}'");
}

Brevada::Redirect('/hub/hub.php');
?>