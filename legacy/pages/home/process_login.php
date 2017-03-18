<?php
$email = Brevada::validate(empty($_POST['email']) ? '' : $_POST['email'], VALIDATE_DATABASE);
$password = Brevada::validate(empty($_POST['password']) ? '' : $_POST['password'], VALIDATE_DATABASE);
		
$dest = '/home/login.php?login=failed';

if(Brevada::Login($email, $password)){
	$dest = '/dashboard';
} else {
	Brevada::Logout();
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>