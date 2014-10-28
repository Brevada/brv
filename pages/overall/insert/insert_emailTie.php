<?php  
$user_id = Brevada::FromPOSTGET('user_id');
$emailTie = Brevada::FromPOSTGET('emailTie');
$session_id = session_id();
	
$sql ="INSERT INTO reviewers(user_id, session_id, email) VALUES('{$user_id}','{$session_id}','{$emailTie}')";
Database::query($sql);
?>