<?php
$user_id = $_SESSION['user_id'];

$user_extension = '';
$query = Database::query("SELECT `id`, `extension` FROM users WHERE `id`='{$user_id}' LIMIT 1");
while($row = $query->fetch_assoc()){
	$user_extension = $row['extension'];
}

$allowedExts = array("jpg", "jpeg", "gif", "png", "bmp");
$extension = strtolower(end(explode(".", $_FILES["file"]["name"])));

//5 MB max.
if (($_FILES["file"]["size"] > 0 && $_FILES["file"]["size"] <= 5242880) && in_array($extension, $allowedExts)) {
	if ($_FILES["file"]["error"] > 0) {
		echo "Invalid file.";
	} else {
		$old = "../user_data/user_images/{$user_id}.{$user_extension}";
		if (file_exists($old)){@unlink($old);}
		
		if(move_uploaded_file($_FILES["file"]["tmp_name"], "../user_data/user_images/{$user_id}.{$extension}") !== false){
			Database::query("UPDATE users SET extension='{$extension}', picture='yes' WHERE id='{$user_id}'");
		}
	}
} else {
	echo "Invalid file.";
}
 
Brevada::Redirect('/hub');
?>