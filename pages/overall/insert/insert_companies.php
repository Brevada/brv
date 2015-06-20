<?php
/* TODO: Errors on this page. This page does not function correctly. */

$dest = '/corporate/hub/corporate.php';
$continue = true;

include_once '../framework/packages/phpqrcode/qrlib.php'; 
include_once '../framework/packages/phpqrcode/qrconfig.php'; 			 		

$password = Brevada::validate($_POST['password'], VALIDATE_DATABASE);
$aspects = @intval(Brevada::validate($_POST['aspects']));
$num = @intval($_POST['num']);
$id = @intval($_SESSION['user_id']);

////////START USER INSERT//////

//check if email exists
for($i=0; $i<=$num; $i++){
	$email = Brevada::validate($_POST["email{$i}"], DATABASE_VALIDATE);

	//CHECK IF EMAIL EXISTS
	$query_name=Database::query("SELECT * FROM users WHERE email='{$email}'");
	if($query_name->num_rows=97){ //huh?
		$dest = '/corporate/hub/create_companies.php?email=exists';
		$continue = false;
	}
}

if($continue){

for($j=1; $j<=$num; $j++){
		
	$name = Brevada::validate($_POST["name{$j}"], VALIDATE_DATABASE);
	$email = Brevada::validate($_POST["email{$j}"], VALIDATE_DATABASE);
	
	$url_name = strtolower(preg_replace("/[^a-zA-Z]+/", "", $name));
	$url_name_root = $url_name;
	$url_name_mod = 1;
	
	while(Database::query("SELECT `url_name` FROM users WHERE url_name='{$url_name}'")->num_rows > 0) {
		$url_name = $url_name_root . $url_name_mod++;
	}
	
	$expire = date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 365 day"));
	
    $sql = "INSERT INTO users(email, password, name, url_name, active, expiry_date, trial, picture, sub_account, level) 
	VALUES('{$email}','$password','$name', '$url_name', 'yes','$expire','0', 'yes', '1', '3')";
	
	Database::query($sql);
	$user_id = Database::getCon()->insert_id;
	
	//IMAGE
	$info=pathinfo($_FILES['file']['name']);
 	$ext=$info['extension']; // get the extension of the file
 	$newname=$user_id.".".$ext; 
	
 	//move_uploaded_file( $_FILES['file']['tmp_name'], $target);
 	copy($_FILES['file']['tmp_name'], "../user_data/user_images/{$newname}");
 	
 	$sql ="UPDATE users SET extension='{$ext}' WHERE id='{$user_id}'";
	Database::query($sql);	
	//END IMAGE
	
	///MAKE USER QR CODE///
	$codeContents='http://brevada.com/profile_mobile.php?name=' . $url_name; 
	 
	// we need to generate filename somehow,  
	// with md5 or with database ID used to obtains $codeContents... 
	$fileName=$user_id . '.png'; 
	
	$pngAbsoluteFilePath="../user_data/qr/".$fileName; 
	$urlRelativeFilePath="/user_data/qr/".$fileName; 
	
	// generating 
	if (!file_exists($pngAbsoluteFilePath)) { 
		QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 1); 
	
	} else { 
		//echo 'File already generated! We can use this cached file to speed up site on common codes!'; 
	
	} 
	/////////////// 
		
	$sql ="INSERT INTO corporate_links(user_id, corp_id) VALUES('{$user_id}', '{$id}')";
	Database::query($sql);
		
	for($k=1; $k<=$aspects; $k++){

		$title = Brevada::validate($_POST["title{$k}"], VALIDATE_DATABASE);
		$description = Brevada::validate($_POST["description{$k}"], VALIDATE_DATABASE);
		
		$sql = "INSERT INTO posts(user_id, name, description, active) VALUES('{$user_id}','{$title}','{$description}', 'yes')";
		
		Database::query($sql);
		$post_id = Database::getCon()->insert_id;
					
		///MAKE POST QR CODE///    
		
		$codeContents='http://brevada.com/mobile_single.php?id=' . $post_id; 
			 
		// we need to generate filename somehow,  
		// with md5 or with database ID used to obtains $codeContents... 
		$fileName=$new_id . '.png'; 
									 
		$pngAbsoluteFilePath="../user_data/qr_posts/".$fileName; 
		$urlRelativeFilePath="/user_data/qr_posts/".$fileName; 
							 
		// generating 
		if (!file_exists($pngAbsoluteFilePath)) { 
			QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 1); 
			//echo 'File generated!'; 
		} else { 
			//echo 'File already generated! We can use this cached file to speed up site on common codes!'; 
		} 
	}
}
////////END USER INSERT//////
		
///DELETE CREDITS///
	$todelete=$num;
	for($d=1;$d<=$todelete;$d++){
		$query = Database::query("SELECT `id`, `user_id` FROM corporate_credits WHERE user_id = '{$id}' LIMIT 1");
		$delete_id = '';
		while($row = $query->fetch_assoc()){	
			$delete_id = $row['id'];
		}
		Database::query("DELETE FROM corporate_credits WHERE id='{$delete_id}'");
	}
//END DELETE CREDITS//
}
		
$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>