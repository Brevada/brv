<?php
$url_name = Brevada::validate($_GET['name'], VALIDATE_DATABASE);

$query = Database::query("SELECT `id` FROM `stores` WHERE `URLName`='{$url_name}' LIMIT 1");

$dest = '/404';
if($query !== false && $query->num_rows > 0){
	$dest = "/profile/profile.php?name={$url_name}";
}

$this->addResource("<link ref='shortcut icon' type='image/x-icon' href='/images/quote.png' />", true, true);
$this->add(new View('../widgets/inlineloader.php', array('destination' => $dest)));
?>