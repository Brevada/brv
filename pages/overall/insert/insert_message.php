<?php
$user_id = @intval($_GET['userid']);
$message = Brevada::FromPOSTGET('message');
$date = date('Y-m-d');
$sql = "INSERT INTO messages(user_id, message, date) VALUES('{$user_id}','{$message}','{$date}')";
Database::query($sql);
?>