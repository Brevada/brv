<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/create_companies.css');
$this->addResource('/js/corporate.js');
$this->addResource('/pages/overall/packages/dygraph-combined.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/login');
}

$user_id=Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$query=Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");

if($query->num_rows == 0){
	Brevada::Redirect('/home/logout');
}

if($user_id != Brevada::validate($_SESSION['corporate_id'])){
	Brevada::Redirect('/hub');
}

$name=""; $email=""; $url_name=""; $active=""; $logins=""; $extension=""; $type=""; $trial=""; $picture=""; $expiry_date=""; $user_extension="";

while($row=$query->fetch_assoc()){
	$name=$row['name'];
	$email=$row['email'];
	$url_name=$row['url_name'];
	$active=$row['active'];
	$logins=$row['logins'];
	$extension=$row['extension'];
	$type=$row['type'];
	$trial=$row['trial'];
	$picture=$row['picture'];
	$expiry_date=$row['expiry_date'];
	$user_extension=$row['extension'];
}

if($expiry_date < date("Y-m-d")){
	$active='no';
}

$message = $active == 'no' ? "You're Almost There!" : 'Membership Expired';

$this->setTitle("Brevada Hub - {$name}");

$this->add(new View('../widgets/corporate/corporate_hub_banner.php', array('user_id' => $user_id, 'user_extension' => $user_extension, 'active' => $active, 'logins' => $logins, 'picture' => $picture)));
?>

<br style="clear:both;" />
<div style="width:720px; margin: 0 auto; margin-top:20px; height:0px; padding-top:0px;">
	<div id="sized_containerHub">	
		<?php
		$num = Brevada::validate($_POST['num']);
		$aspects = Brevada::validate($_POST['aspects']);	
		?>
		<span id="red" style="font-size:17px;">Add <?php echo $num; ?> new identical companies</span> <a href="corporate.php">Go Back</a><br />
		<!-- LEFT (changed) -->
		<div style="float:left; width:350px;  margin-top:0px; padding-left:4px;">
				<form action="/overall/insert/insert_companies.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="num" value="<?php echo $num; ?>" />
					<input type="hidden" name="aspects" value="<?php echo $aspects; ?>" />
					<input id="in" name="password" type="text" placeholder="Default Password For Accounts" style="width:300px; font-size:13px; margin-top:5px;" /><br />
					<div style="font-size:11px; color:#444; margin-top:5px;">Image for new accounts: <input type="file" name="file" /></div><br />
					<?php for($i=0;$i<=$num;$i++){ ?>
					<input id="in"  name="name<?php echo $i; ?>" type="text" placeholder="Company <?php echo $i; ?> name" style="width:300px; font-size:13px; margin-top:5px;" /><br />
					<input id="in"  name="email<?php echo $i; ?>" type="text" placeholder="Company <?php echo $i; ?> email" style="width:300px; font-size:13px; margin-top:5px;" /><br /><br />
					<?php } ?>
					<br />
		</div>
		<div style="float:right; width:350px; margin-top:0px; padding-left:4px;">
					<?php for($j=0;$j<=$aspects;$j++){ ?>
					<input id="in"  name="title<?php echo $j; ?>" type="text" placeholder="Aspect <?php echo $j; ?> title (eg. Customer Service)" style="width:320px; font-size:13px; margin-top:5px;" />
					<br />
					<input id="in"  name="description<?php echo $j; ?>" type="text" placeholder="Description (eg. How well did our staff ensure your satisfaction?)" style="width:320px; font-size:11px; margin-top:5px;" />
					<br />
					<br />
					<?php } ?>
					<input class="button4" type="submit" name="submit" value="Create Accounts" style="width:200px;" />
				</form>
		</div>
		<br style="clear:both;" />
	</div>
</div>
<?php $this->add(new View('../template/footer.php')); ?>