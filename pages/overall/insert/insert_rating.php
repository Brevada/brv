<?php
$value = Brevada::validate(Brevada::FromPOSTGET('value'), VALIDATE_DATABASE);
$user_id = @intval(Brevada::FromPOSTGET('user_id'));
$post_id = @intval(Brevada::FromPOSTGET('post_id'));
$reviewer = Brevada::FromPOSTGET('reviewer');
$session_id = session_id();
$ipaddress = Brevada::FromPOSTGET('ipaddress');
$country = Brevada::validate(Brevada::FromPOSTGET('country'), VALIDATE_DATABASE);
$timezone = 'America/Detroit';

if(!empty($value)){
	date_default_timezone_set($timezone);
	$date = date('Y-m-d H:i:s', time());

	$query = Database::query("SELECT `email`, `user_id`, `id` FROM reviewers WHERE email='{$reviewer}' AND user_id='{$user_id}'");
	
	$reviewer_id = '';

	if(!empty($reviewer)){
		if($query->num_rows==0){
			Database::query("INSERT INTO reviewers(email, user_id) VALUES('{$reviewer}', '{$user_id}')");
			$reviewer_id = Database::getCon()->insert_id;
		} else {
			while($rows=$query->fetch_assoc()){
				$reviewer_id = $rows['id'];
			}
		}
	}

	Database::query("INSERT INTO feedback(post_id, value, ip_address, date, country, user_id, reviewer, session_id) VALUES('{$post_id}','{$value}','{$ipaddress}', '{$date}', '{$country}', '{$user_id}', '{$reviewer_id}', '{$session_id}')");
}
?>