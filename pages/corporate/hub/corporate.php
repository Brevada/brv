<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/corporate.css');
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

$message=$active == 'no' ? "You're Almost There!" : 'Membership Expired';

$this->setTitle("Brevada Hub - {$name}");

$this->add(new View('../widgets/corporate/corporate_hub_banner.php', array('user_id' => $user_id, 'user_extension' => $user_extension, 'active' => $active, 'logins' => $logins, 'picture' => $picture, 'name' => $name, 'url_name' => $url_name)));
?>
<br style="clear:both;" />
<div  style="width:94%; margin: 0 auto; margin-top:0px; height:0px; padding-top:0px; min-width:1150px;">
	<div id="sized_containerHub">	
 	<!-- LEFT (changed) --> 	
		<div style="float:left; width:650px; margin-top:20px; padding-left:4px;">
			<!-- Change Pic Expander Content -->
			<div id="expanderContent6Products" style="display:none; margin-top:10px; padding:4px; width:500px;">
					<form action="/hub/update/picture_change.php" method="post" enctype="multipart/form-data"> 
						<input type="file" name="file" style="float:left; width:150px;" /> 
						<input class="button2" type="submit" name="submit" value="Change" /> 
						<br style="clear:both;" />
					</form>
				<br style="clear:both;" />
				<br style="clear:both;" />
			</div>
			<!-- Password Expander Content -->
			<div id="expanderContent7Products" style="display:none; margin-top:10px; padding:4px;">
				<form action="/hub/update/password_change.php" method="post">
					<input class="inp" id="password1" name="pass" value="New Password" type="text" style="width:210px;" onfocus="if(this.value == 'New Password'){this.value=''; this.type='password';}" onblur="if(this.value == ''){this.value='New Password'; this.type='text';}"></input>
					<input class="inp" id="password1"  value="Retype New Password" type="text" style="width:210px;" onfocus="if(this.value == 'Retype New Password'){this.value=''; this.type='password';}" onblur="if(this.value == ''){this.value='Retype New Password'; this.type='text';}"></input>
					<input class="button2" type="submit" value="Change" />
				</form>
				<br style="clear:both;" />
				<br style="clear:both;" />
			</div>
			<div id="expanderContent5Products" style="display:none; margin-top:10px;">
				<br style="clear:both;" />
				<?php $this->add(new View('../widgets/corporate/new_company.php', array('user_id' => $user_id))); ?>
			</div>
			<div id="expanderContent9Products" style="display:none; margin-top:10px;">
				<form action="/hub/update/info_change.php" method="post">
						<input class="inp"  name="name" value="<?php  echo $name; ?>" type="text" style="width:230px;" onfocus="if(this.value == 'New Password'){this.value=''; }" onblur="if(this.value == ''){this.value='New Password';}"></input>
						<input class="inp" type="email"  name="email" value="<?php  echo $email; ?>" type="text" style="width:230px;" onfocus="if(this.value == 'Retype New Password'){this.value=''; }" onblur="if(this.value == ''){this.value='Retype New Password';}"></input>
						<input class="button2" type="submit" value="Update" style="margin-top:6px;" />
				</form>
				<br style="clear:both;" />
				<br style="clear:both;" />
			</div>
			<br style="clear:both;" />
			<div id="box_holder">
				<?php
				$queryCorp=Database::query("SELECT `corp_id`, `id`, `user_id` FROM corporate_links WHERE corp_id='{$user_id}' ORDER BY `id` DESC");
				$company_id='';
				while($rowCorp=$queryCorp->fetch_assoc()){
					$company_id=$rowCorp['user_id'];
					$this->add(new View('../widgets/corporate/company_box.php', array('company_id' => $company_id)));
				}
				?>
			</div>
		</div>
		<!-- FAR RIGHT -->
		<div style="float:right; width:200px; margin-top:20px;">
			<a href="print_stats.php" target="BLANK"><div class="button4" id="atool">Print Stats</div></a><br />
			<a href="login_info.php" target="BLANK"><div class="button4" id="atool">Login Info</div></a><br style="clear:both;" />
		</div>
		<!-- RIGHT -->
		<div style="float:right; width:200px; margin-top:20px;">
				<div id="corporate_info">
					<div id="corporate_stat">
						<strong><?php echo $queryCorp->num_rows; ?></strong><br />
						<span style="font-size:11px;">Sub-Accounts</span>
					</div>
					<div id="corporate_stat">
					<?php
						$queryCredits=Database::query("SELECT `user_id` FROM corporate_credits WHERE user_id='{$user_id}'");	
					?>
					<strong><?php echo $queryCredits->num_rows; ?></strong> <br />
					<span style="font-size:11px;">Account Credits (<a id="creditsHead" data-reveal-id="unlock" style="cursor:pointer;"><span style="font-size:10px; color:bc0101;">Buy More</span></a>)</span>
					</div>
					<div id="creditsContent" style="display:none; margin-top:0px; padding:0px; width:718px;  border:0px solid #dcdcdc; border-top:0px; ">
						<div id="displayCredit">How Many account credits would you like to buy?</div>
						<form action="corporate_finalize.php" method="post">
							<input class="inp" id="credit" name="num" type="text" />
							<input class="button4" type="submit" value="Buy Credits" />
						</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->add(new View('../hub/includes/marketing/promopopup.php', array('user_id' => $user_id))); ?>
<?php $this->add(new View('../template/footer.php')); ?>