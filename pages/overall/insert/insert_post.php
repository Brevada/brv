<?php
$dest = '/hub';

$user_id = @intval($_POST['user_id']);
$name = Brevada::validate($_POST['name'], VALIDATE_DATABASE);
$description = Brevada::validate($_POST['description'], VALIDATE_DATABASE);
		
$query = Database::query("SELECT * FROM users WHERE id='user_id' LIMIT 1");

$url_name = '';
while($row = $query->fetch_assoc()){$url_name = $row['url_name']; }

$sql = "INSERT INTO posts (`user_id`, `name`, `description`, `active`, `type`) VALUES ('{$user_id}','{$name}','{$description}', 'yes', '')";

Database::query($sql);

$new_id = Database::getCon()->insert_id;
    
$allowedExts=array("jpg", "jpeg", "gif", "png", "bmp");
$extAr = explode(".", strtolower($_FILES["file"]["name"]));
$extension=end($extAr);
if (($_FILES["file"]["size"] < 10485760) && in_array($extension, $allowedExts)) { //10 MB
	if ($_FILES["file"]["error"] > 0) {
		$dest = '/hub'; //error
		$extension = 'none';
	} else {
		if(move_uploaded_file($_FILES["file"]["tmp_name"], "../user_data/post_images/" . $new_id . "." . $extension . "") === false){
			$dest = '/hub'; //error
			$extension = 'none';
		}
	}
} else {
	//echo "Invalid file";
	$extension = 'none';
}
 
///MAKE QR CODE///   
include_once '../framework/packages/phpqrcode/qrlib.php'; 
include_once '../framework/packages/phpqrcode/qrconfig.php'; 

// how to save PNG codes to server 
$codeContents='http://brevada.com/mobile/mobile_single.php?id=' . $new_id;
 
// we need to generate filename somehow,  
// with md5 or with database ID used to obtains $codeContents... 
$fileName=$new_id . '.png'; 

$pngAbsoluteFilePath="../user_data/qr_posts/".$fileName; 
$urlRelativeFilePath="/user_data/qr_posts/". $fileName; 
 
// generating 
if (!file_exists($pngAbsoluteFilePath)) { 
    QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 1); 
    //echo 'File generated!'; 
} else { 
    //echo 'File already generated! We can use this cached file to speed up site on common codes!'; 
} 
  
$sql ="UPDATE posts SET extension='{$extension}' WHERE id='{$new_id}'";
Database::query($sql);

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>