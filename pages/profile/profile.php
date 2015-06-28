<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/profile_header.css');
$this->addResource('/css/profile.css');

$id = @intval(Brevada::validate($_POST['id']));
$url_name=Brevada::validate($_GET['name'], VALIDATE_DATABASE);

$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$condition = empty($url_name) ? "ID = '".$_SESSION['user_id']."'" : "url_name='{$url_name}'";

$query = Database::query("SELECT * FROM users WHERE {$condition} LIMIT 1");

if($query->num_rows == 0) {
	Brevada::Redirect('/404');
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
   $url_name=Brevada::validate($row['url_name']);
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

	
	<div class="topbar">
		<div class="container">
			<div class="title">
				<i class="fa fa-cutlery"></i>   Give <?php echo $name; ?> Feedback 
			</div>
			<div class="icons"><img class="logo" src="iconWhite.png" /></div>
		</div>
	</div>

	<div  class="aspect-container container">
		<?php
		$reviewer=Brevada::validate(empty($_GET['reviewer']) ? '' : $_GET['reviewer']);
		$postQuery=Database::query("SELECT aspects.ID, aspect_type.Title, aspect_type.Description as Description FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.OwnerID = {$id} AND aspects.`Active` = 1 ORDER BY aspects.ID DESC");
		if($postQuery !== false && $postQuery->num_rows > 0){
			while($row=$postQuery->fetch_assoc()) {		
				$this->add(new View('../widgets/profile/post_box.php', array('row' => $row, 'reviewer' => $reviewer, 'country' => $country, 'ip' => $ip, 'id' => $user_id, 'user_extension' => $user_extension)));
			}
		}
		?>	
	</div>
<?php $this->add(new View('../template/footer.php')); ?>