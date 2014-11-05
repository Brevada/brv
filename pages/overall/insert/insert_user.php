<?php
$dest = '/hub';

$email = Brevada::validate($_POST['email'], VALIDATE_DATABASE);
$password = Brevada::validate($_POST['password'], VALIDATE_DATABASE);
$password2 = Brevada::validate($_POST['password2'], VALIDATE_DATABASE);
$name = Brevada::validate($_POST['name'], VALIDATE_DATABASE);
$level = @intval(Brevada::validate($_POST['level']));

if(empty($email) || empty($password) || empty($name) || $email == 'Email' || $password == 'Password' || $name == 'Your Company Name'){
	$dest = '/home/signup.php';
} else {
	//CHECK IF EMAIL EXISTS
	$query_name = Database::query("SELECT `email` FROM users WHERE email = '{$email}' LIMIT 1");
	
	if($query_name->num_rows > 0){
		$dest = '/home/signup.php?email=exists';
	} else {
		//CHECK FOR NAME
		
		$url_name = strtolower(preg_replace("/[^a-zA-Z]+/", "", $name));
		$url_name_root = $url_name;
		$url_name_mod = 1;
		
		while(Database::query("SELECT `url_name` FROM users WHERE url_name='{$url_name}'")->num_rows > 0) {
			$url_name = $url_name_root . $url_name_mod++;
		}
		
		$expire = date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 365 day"));
		$sql ="INSERT INTO users(email, password, name, url_name, active, expiry_date, trial, level) VALUES('{$email}','{$password}','{$name}', '{$url_name}', 'no','{$expire}','0', '{$level}')";
		$query = Database::query($sql);
		
		$_SESSION['user_id'] = Database::getCon()->insert_id;
		$user_id = $_SESSION['user_id'];
		$sql = "INSERT INTO posts(user_id, name, description, active) VALUES('{$user_id}','Overall Satisfaction','Were you satisfied with {$name}?', 'yes')";
		$post_id = Database::query($sql);
		$post_id = Database::getCon()->insert_id;

		// -- Function Name : generateRandomString
		// -- Params : $length=6
		// -- Purpose : 
		function generateRandomString($length=6) {
			$characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString='';
			for ($i=0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}

			return $randomString;
		}

		$referral_code = generateRandomString();
		
		while(Database::query("SELECT `code` FROM codes WHERE code='{$referral_code}'")->num_rows > 0) {
			$referral_code = generateRandomString();
		}
		
		$sql = "INSERT INTO codes(code, value, duration_months, notes, uses, referral_user) 
		VALUES('{$referral_code}','300.00','12', '{$name}', '10', '{$user_id}')";
		Database::query($sql);
		///MAKE POST QR CODE///    
		include_once '../framework/packages/phpqrcode/qrlib.php'; 
		include_once '../framework/packages/phpqrcode/qrconfig.php'; 
		// how to save PNG codes to server 
		$codeContents='http://brevada.com/mobile_single.php?id=' . $post_id; 
		// we need to generate filename somehow,  
		// with md5 or with database ID used to obtains $codeContents... 
		$fileName=$post_id . '.png';
		$pngAbsoluteFilePath="../user_data/qr_posts/".$fileName;
		$urlRelativeFilePath="/user_data/qr_posts/".$fileName; 

		// generating 
		
		if (!file_exists($pngAbsoluteFilePath)) {
			QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 1);
			//echo 'File generated!'; 
		} else {
			//echo 'File already generated! We can use this cached file to speed up site on common codes!'; 
		}

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
			//echo 'File generated!'; 
		} else {
			//echo 'File already generated! We can use this cached file to speed up site on common codes!'; 
		}

		//include'../email/emails/mail_1.php';
		/////////////// 
		
		$posts_tokens = explode(',', $_POST['posts-token']);
		if($posts_token !== false){
			foreach($posts_tokens as $token){
				$token = Brevada::validate($token, VALIDATE_DATABASE);
				if(!empty($token)){
					$tokenQuery = Database::query("INSERT INTO posts (`user_id`, `name`, `description`, `active`, `type`) VALUES ('{$user_id}','{$token}','', 'yes', '')");
					if($tokenQuery !== false){
						$new_id = Database::getCon()->insert_id;
						Brevada::GeneratePostQR(URL.'/mobile/mobile_single.php?id=' . $new_id);
					}
				}
			}
		}
		
		//REDIRECTIONS:
		if($level==1){
			$dest = '/hub/hub.php';
		}
		else{
			$dest = '/hub/payment/payment.php';
		}
	}
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>