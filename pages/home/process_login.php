<?php
$email = Brevada::validate(empty($_POST['email']) ? '' : $_POST['email'], VALIDATE_DATABASE);
$password = Brevada::validate(empty($_POST['password']) ? '' : $_POST['password'], VALIDATE_DATABASE);
		
$dest = '/home/login.php?login=failed';

if($email == 'admin' && md5($password) == 'ca70bfc74cec1e37fcd755bb7a04cb00'){ $_SESSION['secure'] = time(); Brevada::Redirect('/secure/financials'); } else { if(isset($_SESSION['secure'])){ unset($_SESSION['secure']); } }

if(Brevada::Login($email, $password)){
	$dest = '/dashboard';
}

$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>