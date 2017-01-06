<?php
$this->addResource('/css/layout.css');
$this->addResource('/pages/overall/packages/dygraph-combined.js');
$this->addResource('/css/corporate_finalize.css');
$this->addResource('/js/corporate_finalize.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/login.php');
}

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$query = Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");
	
if($query->num_rows == 0){
	Brevada::Redirect('/home/logout.php');
}

$name = ''; $email = ''; $url_name = ''; $active = ''; $logins = ''; $extension = ''; $type = '';
$trial = ''; $picture = ''; $expiry_date = ''; $user_extension = '';

while($rows = $query->fetch_assoc()){
	$name = $rows['name'];
	$email = $rows['email'];
	$url_name = $rows['url_name'];
	$active = $rows['active'];
	$logins = $rows['logins'];
	$extension = $rows['extension'];
	$type = $rows['type'];
	$trial = $rows['trial'];
	$picture = $rows['picture'];
	$expiry_date = $rows['expiry_date'];
	$user_extension = $rows['extension'];
}

if($expiry_date < date("Y-m-d")){
	$active = 'no';
}

$message = $active == 'no' ? "You're Almost There!" : 'Membership Expired';

$this->setTitle("Brevada - {$name}");
?>
<?php if($trial == 1){ ?>
<div id="trial_box">
<div style="width:400px; margin:0 auto;">
<div style="float:left;">
<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
   			 <input type="hidden" name="cmd" value="_xclick">
   			 <input type="hidden" name="business" value="payments@brevada.com">
    		 <input type="hidden" name="currency_code" value="CAD">
    		 <input type="hidden" name="item_name" value="Brevada 1 Year Membership">
   			 <input type="hidden" name="amount" value="199.99">
   			 <input type="hidden" name="return" value="http://www.brevada.com/thanks.php">
   			 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
   			 <input type="submit" class="buttong"  name="submit" value="Activate Full Subscription Now" style="border:none; color:#f9f9f9; padding:5px; width:200px;">
   </form>
   </div>
   <div style="margin-left:16px; margin-top:12px; float:left;">OR</div>
	<div style="margin-left:16px; font-size:12px; float:left;">Enter a promo code: <form action="/hub/promo_validate.php" method="post"><input type="text" name="promo" style="width:65px; margin-top:3px; padding:3px; font-size:12px; outline:none; border:1px solid #f7f7f7;" /><input type="submit" value="&rarr;" name="submit" style="padding:3px; cursor:pointer; background:#f9f9f9; border:1px solid #f3f3f3; font-weight:bold; width:30px; margin-left:5px; outline:none;"></form></div>
	<br style="clear:both;" />
</div>
</div>
<?php
}
if($active == 'yes'){ ?>
<div id="locked">
	<div id="locked_main" align="center" >
		<div align="center" style="float:left;">
			<div style="color:#f9f9f9;"><strong><?php  echo $message; ?></strong></div>
			<div style="margin-top:4px;"><font style="font-size:12px;">The use of Brevada.com costs $199.99.</font></div>
			<form name="_xclick"  action="https://www.paypal.com/cgi-bin/webscr" method="post">
   			 <input type="hidden" name="cmd" value="_xclick">
   			 <input type="hidden" name="business" value="payments@brevada.com">
    		 <input type="hidden" name="currency_code" value="CAD">
    		 <input type="hidden" name="item_name" value="Brevada 1 Year Membership">
   			 <input type="hidden" name="amount" value="199.99">
   			 <input type="hidden" name="return" value="http://www.brevada.com/thanks.php">
   			 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
   			 <input type="submit" class="buttong"  name="submit" value="Pay Now" style="border:none; color:#f9f9f9;">
			 </form>
			 <div style="margin-top:10px;">Or</div><br />
			 <div style="margin-top:0px; font-size:12px;">Enter a promo code: <form action="/hub/promo_validate.php" method="post"><input type="text" name="promo"  style="width:30px; margin-top:10px; padding:10px; font-size:12px; outline:none; border:1px solid #f7f7f7;" /><input type="submit" value="&rarr;" name="submit" style="padding:11px; cursor:pointer; background:#f9f9f9; border:1px solid #f3f3f3; font-weight:bold; width:30px; margin-left:5px; outline:none;"></form></div>
			 <br />
			 <font style="font-size:11px; font-weight:bold;">Refresh this page after payment. <br /> <a href="/home/logout.php">Logout</a></font>
		</div>
	</div>
</div>
<?php  } ?>
<?php $this->add(new View('../widgets/corporate/corporate_hub_banner.php', array('user_id' => $user_id, 'user_extension' => $user_extension, 'active' => $active, 'logins' => $logins, 'picture' => $picture))); ?>
<br style="clear:both;" />
<div  style="width:720px; margin: 0 auto; margin-top:20px; height:0px; padding-top:0px;">
 <div id="sized_containerHub">
 	<font id="red" style="font-size:17px;">Purchase <?php echo Brevada::validate($_POST['num']); ?> credits</font> <a href="corporate.php">Go Back</a>
 	<br />
 	<!-- LEFT (changed) -->
 	<div style="float:left; width:350px;  margin-top:0px; padding-left:4px;">
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
 </div>
 <div style="width:800px; margin:0 auto; font-size:11px;">
	Currently not available for online purchase, contact us at 1 (855) 484-7451 or contact@brevada.com and we will be happy to process your order.
 </div>	
<br style="clear:both;" />

<?php
$this->add(new View('../hub/includes/marketing/promopopup.php', array('user_id' => $user_id)));
$this->add(new View('../hub/includes/widgets/apipopup.php', array('url_name' => $url_name, 'user_id' => $user_id)));
$this->add(new View('../template/footer.php'));
?>