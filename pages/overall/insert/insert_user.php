<?php
/* QR CODE */   
include_once '../framework/packages/phpqrcode/qrlib.php'; 
include_once '../framework/packages/phpqrcode/qrconfig.php';

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

$dest = '/hub';
$email = Brevada::validate($_POST['email'], VALIDATE_DATABASE);
$password = Brevada::validate($_POST['password'], VALIDATE_DATABASE);
$password2 = Brevada::validate($_POST['password2'], VALIDATE_DATABASE);
$name = Brevada::validate($_POST['name'], VALIDATE_DATABASE);

/* TODO: Verify level. At the moment, this can easily be modified by end-user. */

$level = @intval(Brevada::validate($_POST['level']));
$level = 1;

if(empty($email) || empty($password) || empty($name) || $email == 'Email' || $password == 'Password' || $name == 'Your Company Name'){
	$dest = '/home/signup.php';
} else {
	//CHECK IF EMAIL EXISTS
	$query_name = Database::query("SELECT `email` FROM users WHERE email = '{$email}' LIMIT 1");
	
	if($query_name->num_rows > 20){
		$dest = '/home/signup.php?email=exists';
	} else {
		//CHECK FOR NAME
		
		$url_name = strtolower(preg_replace("/[^a-zA-Z]+/", "", $name));
		$url_name_root = $url_name;
		$url_name_mod = 1;
		
		while(Database::query("SELECT `url_name` FROM users WHERE url_name='{$url_name}'")->num_rows > 0) {
			$url_name = $url_name_root . $url_name_mod++;
		}
		$active = 'no';
		$expiry_date = "NOW() + INTERVAL 365 DAY";
		$password = Brevada::HashPassword($password);
		$trial = 0;

		//$stmt = Database::prepare("INSERT INTO users (email, password, name, url_name, active, expiry_date, trial, level) VALUES (?, ?, ?, ?, 'no', (NOW() + INTERVAL 365 DAY), 0, ?)"));
		if(($stmt = Database::prepare("INSERT INTO users (email, password, name, url_name, active, expiry_date, trial, level) VALUES (?, ?, ?, ?, ?, ({$expiry_date}), ?, ?)")) !== false){
			$stmt->bind_param('sssssii', $email, $password, $name, $url_name, $active, $trial, $level);
			
			if($stmt->execute()){
				$_SESSION['user_id'] = $stmt->insert_id;
				$user_id = $_SESSION['user_id'];
				
				$stmt->close();
				
				if(Database::query("INSERT INTO dashboard_settings () VALUES ()")){
					$dashboardSettingsID = Database::getCon()->insert_id;
					Database::query("INSERT INTO dashboard (`OwnerID`, `SettingsID`) VALUES ({$user_id}, {$dashboardSettingsID})");
				}

				$referral_code = generateRandomString();
				
				while(Database::query("SELECT `code` FROM codes WHERE code='{$referral_code}'")->num_rows > 0) {
					$referral_code = generateRandomString();
				}
				
				$sql = "INSERT INTO codes(code, value, duration_months, notes, uses, referral_user) 
				VALUES('{$referral_code}','300.00','12', '{$name}', '10', '{$user_id}')";
				Database::query($sql);
				
				// how to save PNG codes to server 

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
				if($posts_tokens !== false){
					foreach($posts_tokens as $token){
						$token = Brevada::validate($token, VALIDATE_DATABASE);
						if(!empty($token)){
							if(($stmt = Database::prepare("INSERT INTO aspects (`OwnerID`, `AspectTypeID`) SELECT users.id, (SELECT aspect_type.ID FROM aspect_type WHERE aspect_type.ID = ?) as AspectTypeID FROM users WHERE users.id = ?")) !== false){
								$stmt->bind_param('ii', $token, $user_id);
								if($stmt->execute()){
									$new_id = $stmt->insert_id;
									Barcode::GeneratePostQR(URL.'/mobile/mobile_single.php?id=' . $new_id, $new_id);
								}
								$stmt->close();
							}
						}
					}
				}

				
				//REDIRECTIONS:
				if($level==1){
					$dest = '/dashboard/';
				}
				else{
					$dest = '/hub/payment/payment.php';
				}
				
			} else {
				
				$stmt->close();
				$dest = '/home/signup.php?error';
			}
		}

	}
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>