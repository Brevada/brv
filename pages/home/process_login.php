<?php
//Passwords should not be stored in plain text.

$email = Brevada::validate(empty($_POST['email']) ? '' : $_POST['email'], VALIDATE_DATABASE);
$password = Brevada::validate(empty($_POST['password']) ? '' : $_POST['password'], VALIDATE_DATABASE);
		
$dest = '/home/login.php?login=failed';

$query = Database::query("SELECT `id`, `email`, `password` FROM users WHERE email='{$email}' LIMIT 1");

while($row=$query->fetch_assoc()){
   $real_password = $row['password'];
   $user_id = $row['id'];
   
	if($password==$real_password) {
		$_SESSION['user_id'] = $user_id;

		if(Brevada::IsMobile()){
			$dest = '/mobile/hub_mobile.php';
		} else {
			$dest = '/hub/hub.php';
		}
   }
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>