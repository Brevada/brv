<?php
$this->add(new View('../template/main_header.php'));
$this->addResource('/css/promo_validate.css');

$user_id = $_SESSION['user_id'];
$code = Brevada::validate(Brevada::FromPOSTGET('promo'), VALIDATE_DATABASE);
?>

<div style="width:100%; margin-top:0px; height:670px; background-repeat:repeat-y; background-repeat:repeat-x;">
	<div id="sized_container" style="padding-top:20px;">	
		<div id="black" align="center" style="margin-top:15%; padding-top:20px;padding-bottom:20px; color:#555;">
		<?php
		$query=Database::query("SELECT * FROM codes WHERE code = '{$code}' AND uses > 0 LIMIT 1");
		if($query->num_rows==0){
?>
		<div style="padding:5px; background:red; color:#f9f9f9; width:300px;">Invalid Code</div> 
		<a href="/hub" style="text-decoration:none;"><div class="button_pay" style="width:100px; margin-top:10px;">Go Back</div></a>
<?php
		} else {
		$value = ''; $level = ''; $referral_user = '';		
		while($row = $query->fetch_assoc()){
		   $value = $row['value'];
		   $level = $row['level'];
		   $referral_user = $row['referral_user'];
		}
		
		//get referral user info if applicable
		$referral_name = '';
		if($referral_user!=0){
			$query = Database::query("SELECT `id`, `name` FROM users WHERE `id`='{$referral_user}' LIMIT 1");
			while($row = $query->fetch_assoc()){
				$referral_name = $row['name'];
			}
		}
		
		if($value == 0){
			$expire = date('Y-m-d', strtotime(date("Y-m-d", time()) . " + 365 day"));
			Database::query("UPDATE users SET active='yes', level='{$level}', trial='no', expiry_date='{$expire}', promo_code='FREE' WHERE id='{$user_id}'");
?>
		<div style="padding:5px; background:green; color:#f9f9f9; width:300px;">Brevada Unlocked For Free!</div> 
		<a href="/hub" style="text-decoration:none;"><div class="button_pay" style="width:200px; margin-top:10px;">Go To Your Profile</div></a>
<?php
} else {
	Database::query("UPDATE users SET level='{$level}' WHERE id='{$user_id}'");
	Database::query("INSERT INTO payments(user_id, value, promo_code) VALUES('{$user_id}', '{$value}', '{$code}')");
?>
<div style="padding:5px; background:green; color:#f9f9f9; width:300px;">Valid Code</div><br />
Your Price: $<?php echo $value; ?>
<?php if(!empty($referral_user)){ ?>
<span style="font-size:11px;"> courtesy of: <strong><?php echo $referral_name; ?></strong></span>
<?php } ?>
<br /><br />
<form name="_xclick" action="https://www.paypal.com/ca/cgi-bin/webscr" method="post">
   	 <input type="hidden" name="cmd" value="_xclick">
   	 <input type="hidden" name="business" value="payments@brevada.com">
     <input type="hidden" name="currency_code" value="CAD">
     <input type="hidden" name="item_name" value="Brevada 1 Year Membership">
   	 <input type="hidden" name="amount" value="<?php echo $value; ?>">
   	 <input type="hidden" name="return" value="http://www.brevada.com/hub/thanks.php">
   	 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
   	 <input type="submit" class="button_pay"  name="submit" value="Pay Now">
</form>
<?php
	}
} 
?>
		</div>
	</div>
	<br style="clear:both;" />	
</div>
<?php $this->add(new View('../template/footer.php')); ?>