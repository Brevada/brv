<?php
$user_id = @intval(Brevada::validate(Brevada::FromPOSTGET('user_id')));
$corp_id = @intval($_SESSION['user_id']);

//Check if company owns this sub account.
$query = Database::query("SELECT `corp_id`, `user_id` FROM corporate_links WHERE corp_id='{$corp_id}' AND user_id='{$user_id}' LIMIT 1");

if($query->num_rows > 0){
	$_SESSION['user_id'] = $user_id;
}

$this->addResource('/css/layout.css');
$this->add(new View('../widgets/loader.php', array('destination' => '/dashboard')));
?>