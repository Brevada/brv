<?php
$user_id = Brevada::FromPOSTGET('user_id');
$_SESSION['user_id'] = $user_id;

$this->add(new View('../widgets/loader.php', array('destination' => '/hub')));
?>