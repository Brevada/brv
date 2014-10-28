<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/login_info.css');

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$queryCorp=Database::query("SELECT `corp_id`, `user_id`, `id` FROM `corporate_links` WHERE `corp_id`='{$user_id}' ORDER BY `id` DESC");
	
	if($queryCorp->num_rows == 0){
		echo "No associated sub-accounts.";
	}

	while($rowCorp=$queryCorp->fetch_assoc()){
		$company_id=$rowCorp['user_id'];
		
		$queryComp=Database::query("SELECT `id`, `name`, `email`, `password` FROM `users` WHERE `id`='{$company_id}' ORDER BY `id` DESC");
		while($rowComp=$queryComp->fetch_assoc()){
			$user_name=$rowComp['name'];
			$user_email=$rowComp['email'];
			$user_password=$rowComp['password'];
?>
			<div id="stats_box">
				<div class="stats_left" style="font-size:15px;">	
					<strong><?php echo $user_name; ?></strong><span style="font-size:11px;"><br />Email: <?php echo $user_email; ?><br />Password: <?php echo $user_password; ?></span>
				</div>
				<br style="clear:both;" />
			</div>
<?php
		}
	}
?>