<?php
$this->IsScript = true;
date_default_timezone_set('America/New_York');

$rating = Brevada::validate(Brevada::FromPOSTGET('value'), VALIDATE_DATABASE);
$aspectID = @intval(Brevada::FromPOSTGET('post_id'));

// If the user clears their cookies while on the page:
if(empty($_SESSION['SessionCode'])){
	$_SESSION['SessionCode'] = strval(bin2hex(openssl_random_pseudo_bytes(16)));
}
$sessionCode = isset($_SESSION['SessionCode']) ? $_SESSION['SessionCode'] : '';

/* TODO: Replace these requires with proper API call. */
require_once 'classes/TaskLoader.php';
require_once 'classes/BrevadaAPI.php';
require_once 'classes/APITasks/Task.Feedback.class.php';
TaskFeedback::insertRating($rating, $aspectID, $sessionCode);

exit('OK');
?>