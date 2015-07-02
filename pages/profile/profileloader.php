<?php
$url_name = Brevada::validate($_GET['name'], VALIDATE_DATABASE);

$query = Database::query("SELECT `id`, `name`, `type`, `extension`, `corporate` FROM users WHERE url_name='{$url_name}' AND active='yes' LIMIT 1");

$dest = '/404';
if($query !== false && $query->num_rows > 0){
	if(Brevada::IsMobile()){
		//$dest = "/mobile/profile/profile.php?name={$url_name}";
		$dest = "/profile/profile.php?name={$url_name}";
	} else {
		$dest = "/profile/profile.php?name={$url_name}";
	}
}

$this->addResource("<link ref='shortcut icon' type='image/x-icon' href='/images/quote.png' />", true, true);
$this->add(new View('../widgets/inlineloader.php', array('destination' => $dest)));
?>