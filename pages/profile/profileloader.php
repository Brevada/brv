<?php
$url_name = Brevada::validate($_GET['name'], VALIDATE_DATABASE);

$query = Database::query("SELECT `id`, `name`, `type`, `extension`, `corporate` FROM users WHERE url_name='{$url_name}' AND active='yes' LIMIT 1");

$dest = $query != false && $query->num_rows > 0 ? "/profile/profile.php?name={$url_name}" : "/404";

$this->addResource("<link ref='shortcut icon' type='image/x-icon' href='/images/quote.png' />", true, true);
$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>