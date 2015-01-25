<?php
//Passwords should not be stored in plain text.

$email = Brevada::validate(empty($_POST['email']) ? '' : $_POST['email'], VALIDATE_DATABASE);
$password = Brevada::validate(empty($_POST['password']) ? '' : $_POST['password'], VALIDATE_DATABASE);
		
$dest = '/home/login.php?login=failed';

if($email == 'admin' && md5($password) == 'ca70bfc74cec1e37fcd755bb7a04cb00'){ $_SESSION['secure'] = time(); Brevada::Redirect('/secure/financials'); } else { if(isset($_SESSION['secure'])){ unset($_SESSION['secure']); } }

$query = Database::query("SELECT `id`, `email`, `password` FROM users WHERE email='{$email}' LIMIT 1");

while($row=$query->fetch_assoc()){
   $real_password = $row['password'];
   $user_id = $row['id'];
   
	if($password==$real_password) {
		$_SESSION['user_id'] = $user_id;

		$dest = '/hub/';
   }
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>