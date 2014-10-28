<?php
$level = @intval(Brevada::validate($_GET['l']));
$user_id = $_SESSION['user_id'];

Database::query("UPDATE users SET active='no', level='{$level}' WHERE id='{$user_id}' LIMIT 1");
Brevada::Redirect('/hub');
?>