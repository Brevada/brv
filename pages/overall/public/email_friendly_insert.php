<?php 
$user_id = @intval(Brevada::validate($_GET['user_id']));
$email = Brevada::validate($_GET['email'], VALIDATE_DATABASE);
$timezone='America/Detroit';
date_default_timezone_set($timezone);
$date = date('Y-m-d H:i:s a', time());

if(!empty($email)){
	//EMAIL INSERT		
	$reviewer_id = '';

	$query = Database::query("SELECT * FROM reviewers WHERE email = '{$email}' AND user_id='{$user_id}'");

	if($query->num_rows==0){	
		$sql = "INSERT INTO reviewers(email, user_id) VALUES('{$email}', '{$user_id}')";
		Database::query($sql);
		$reviewer_id = Database::getCon()->insert_id;
	} else {
		while($row = $query->fetch_assoc()){
			$reviewer_id = $row['id'];
		}
	}


	//RATING INSERT
	$query = Database::query("SELECT `id`, `user_id` FROM posts WHERE user_id='{$user_id}' ORDER BY id DESC");

	while($rows=$query->fetch_assoc()){
		$post_id = $rows['id'];
		$value = Brevada::validate($_GET["r{$post_id}"], VALIDATE_DATABASE);
		if(!empty($value)){
			Database::query("INSERT INTO feedback(post_id, value, reviewer, date, user_id) VALUES('{$post_id}','{$value}','{$reviewer_id}', '{$date}', '{$user_id}')");
		}
	}
}
		
$dest = "/overall/public/rating_thanks.php?u={$user_id}";
$this->add(new View('../widgets/loader.php', array('destination' => $dest)));
?>