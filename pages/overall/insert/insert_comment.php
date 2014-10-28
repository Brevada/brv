<?php
$post_id = @intval(Brevada::validate(Brevada::FromPOSTGET('post_id')));
$country = Brevada::validate(Brevada::FromPOSTGET('country'), VALIDATE_DATABASE);
$user_id = @intval(Brevada::validate(Brevada::FromPOSTGET('user_id')));
$comment = Brevada::validate(Brevada::FromPOSTGET('comment'), VALIDATE_DATABASE);
$ipaddress = Brevada::validate(Brevada::FromPOSTGET('ipaddress'), VALIDATE_DATABASE);
$session_id = session_id();

date_default_timezone_set('America/Detroit');
$date = date('Y-m-d H:i:s', time());

Database::query("INSERT INTO comments(post_id, comment, ip_address, country, `date`, user_id, session_id) VALUES('{$post_id}','{$comment}','{$ipaddress}', '{$country}', '{$date}', '{$user_id}', '{$session_id}')");
?>