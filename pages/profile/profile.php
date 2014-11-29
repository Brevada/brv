<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/profile_header.css');
$this->addResource('/css/profile.css');

$id = @intval(Brevada::validate($_POST['id']));
$url_name=Brevada::validate($_GET['name'], VALIDATE_DATABASE);

//Test for mobile
if(Brevada::IsMobile()){
	Brevada::Redirect("/mobile/profile.php?name={$url_name}");
}

$geo = Brevada::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$query = Database::query("SELECT * FROM users WHERE url_name='{$url_name}' LIMIT 1");

if($query->num_rows == 0) {
	Brevada::Redirect('/profile/not_found.php');
}

$id='';
$user_id='';
$name='';
$type='';
$user_extension='';
$corporate='';

while($row=$query->fetch_assoc()){
   $id=Brevada::validate($row['id']);
   $user_id=$id;
   $name=Brevada::validate($row['name']);
   $type=Brevada::validate($row['type']);
   $user_extension=Brevada::validate($row['extension']);
   $corporate=Brevada::validate($row['corporate']);
}

if($corporate == '1'){
	Brevada::Redirect("/corporate/profile/corporate_profile.php?name={$url_name}");
}

$this->setTitle("Give {$name} Feedback");

$this->addResource("<meta property='og:title' content='Give us feedback!'/>", true, true);
$this->addResource("<meta property='og:type' content='website'/>", true, true);
$this->addResource("<meta property='og:url' content='http://brevada.com/{$url_name}'/>", true, true);
$this->addResource("<meta property='og:image' content='http://brevada.com/images/square_logo.png'/>", true, true);
$this->addResource("<meta property='og:site_name' content='Brevada'/>", true, true);
$this->addResource("<meta property='og:description' content='Give {$name} Feedback on Brevada'/>", true, true);
?>

<div id="banner_main">
	<div id="banner_main_content">
		<div id="banner_main_logo" style="outline:none;">
		<a href="/index.php" style="outline:none;"><img src="/images/brevada.png" style="height:30px; margin-top:4px;" /></a>
		</div>
		<br style="clear:both;" />
	</div>
</div>

<div  style="width:1030px; margin: 0 auto; margin-top:0px;  padding-top:0px;">
	<div id="sized_containerHub">	
		<div  style="float:left; width:250px; margin-top:15px;">
		<div id="far_left">	
			<?php $this->add(new View('../widgets/profile/profile_info_pod.php', array('user_id' => $user_id, 'name' => $name, 'type' => $type, 'user_extension' => $user_extension))); ?>
			<br style="clear:both;" />
 			<?php
			$session_id=session_id(); 
			
			$querySession=Database::query("SELECT * FROM reviewers WHERE session_id='{$session_id}' AND user_id='{$user_id}'");
		
			if($querySession == false || $querySession->num_rows == 0){
			?>	
            <a href="/home/prizes.php" target="_blank" style="text-decoration:none;">
		    <div id="prizes" style="display:none;">	
                    <img src="/images/brevada_prizes.png" style="width:70px; margin-bottom:5px;"/><br />
                   	<span style="font-size:10px; text-decoration:none;">Connect &darr; for a chance to win $200 </span>    
			</div>
            </a>
			<div id="side_box" style="width:220px; min-height:30px; max-height:350px;  overflow:scroll; margin-top:5px; margin-bottom:10px;">
				<?php $this->add(new View('../widgets/profile/communicate_pod.php', array('user_id' => $user_id))); ?>
			</div>
			<?php } else { ?>
			<div id="side_box" style="width:220px; min-height:30px; max-height:350px;  overflow:scroll; margin-top:0px; margin-bottom:10px;">
			<div class="thanks_suggestion" style="display:block;">
				Email Connected!
			</div>
			</div>
			<?php } ?>
			<div id="side_box" style="width:220px; min-height:30px; max-height:350px;  overflow:scroll;overflow-x:hidden; ">
				<div id="suggestion_box"><?php $this->add(new View('../widgets/profile/suggestion_box.php', array('id' => $id))); ?></div>
				<div id="thanks_suggestion" class="thanks_suggestion">Submitted</div>
			</div>
		</div>
	</div>
 	
 	<!-- RIGHT -->	
 	<div style="float:left; width:520px; overflow:hidden;">
	<?php
	$reviewer=Brevada::validate(empty($_GET['reviewer']) ? '' : $_GET['reviewer']);
	$postQuery=Database::query("SELECT * FROM posts WHERE user_id = '{$id}' AND active='yes' ORDER BY id DESC");
	if($postQuery !== false && $postQuery->num_rows > 0){
		while($row=$postQuery->fetch_assoc()) {		
			$this->add(new View('../widgets/profile/post_box.php', array('row' => $row, 'reviewer' => $reviewer, 'country' => $country, 'ip' => $ip, 'id' => $user_id, 'user_extension' => $user_extension)));
		}
	}
	?>	
	</div>
</div>
</div>
<?php $this->add(new View('../template/footer.php')); ?>