<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/financials.css');
?>

<div id="outer">

<!-- USERS -->
<div id="holder" class="text_clean">
<strong>Users</strong><br />

<?php
$userQuery=Database::query("SELECT `name`, `id`, `trial`, `email`, `promo_code`, `expiry_date`, `sub_account`, `active` FROM users ORDER by id DESC");

if($userQuery->num_rows == 0) {
	echo "<br /><span style='font-size:12px; color:#777; font-family:helvetica;'><center>No Data.</center></span>";
} else {
	while($row=$userQuery->fetch_assoc()) {
		$name=Brevada::validate($row['name']);
		$user_id=Brevada::validate($row['id']);
		$trial=Brevada::validate($row['trial']);
		$email=Brevada::validate($row['email']);
		$promo_code=strtolower(Brevada::validate($row['promo_code']));
		$expiry=Brevada::validate($row['expiry_date']);
		$sub_account=Brevada::validate($row['sub_account']);
		$active=Brevada::validate($row['active']);
		
		$price=$promo_code == 'free' || $trial == 1 ? 0 : 199.99;
		
		/*
		if($sub_account == 1){
			$price='Sub-Account';
		} else if(!empty($promo_code) && $promo_code != 'none'){
			$codeQuery=Database::query("SELECT `code`, `value` FROM codes WHERE code={$promo_code}' LIMIT 1");
			$codeRow=$codeQuery->fetch_assoc();
			if(!empty($codeRow)){
				$price=$codeRow['value'];
			}
		}
		*/
		
		//Start Print HTML for Users
		?>
		

		<div id="financial_box">
			<div id="left">
				<strong><?php echo $name; ?></strong><br />
				<span style="font-size:11px;"><?php echo $email; ?></span>
			</div>
			<div id="left" style="width:120px; font-size:12px; font-weight:bold;">
			
			<?php
				if($trial == 1){
					echo "<span style='color:#bc0101;'>On Trial</span>";
				} else if($active != 'yes') {
					echo "<span style='color:red;'>Expired</span>";
				} else {
					echo "<span style='color:green;'>Full Access</span>";
				}
			?>
			
				<br /><span style="font-size:10px; font-weight:normal;">Expires: <?php echo $expiry; ?></span>
			</div>
			<div id="left" style="width:100px; font-size:12px;">
					$<?php echo $price; ?><br />
					<?php
						if(!empty($code)){
							echo "<span style='font-size:10px; color:green;'>({$code})</span>";
						}
					?>
			</div>
			<div id="left" style="width:30px; font-size:12px; overflow:hidden;">
				<form action="secure_login.php" method="post" target="BLANK">
					<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
					<input type="submit" class="button4" value="In" name="submit" style="width:30px; font-size:11px; padding:3px;" />
				</form>
			</div>
			<br style="clear:both;" />
		</div>

		<?php
		//End Print HTML for Users
	}
}
?>

</div>

<!-- CODES -->
<div id="holder" class="text_clean" style="width:280px;">
	<div id="maker" class="text_clean">
	<strong>Make a Promo Code</strong> <br />
			<form action="insert_code.php" method="post">
      			Code <input class="inp" name="code" placeholder="Code" style="float:none;" /><br />
      			Note (optional) <input class="inp" name="note" placeholder="Note" style="float:none;" /><br />
      			Value ($CAD) <input class="inp" name="value" placeholder="Value" style="float:none; width:40px;" /><br />
      			Uses <input class="inp" name="uses" placeholder="Uses" style="float:none; width:40px;" /><br />
      			Duration (months) <input class="inp" name="duration" placeholder="Duration" style="float:none; width:40px;" /><br />
                Level
                <select  name="level">
                  <option value="2">Personal</option>
                  <option value="3" selected>Professional</option>
                  <option value="4">Enterprise</option>
                </select>
				<br />
      			<input class="button" name="sub" type="submit" value="Create" style="width:60px; margin:2px; box-shadow:none;float:none;" />
 			</form>
	</div>
	<br style="clear:both;" />

<?php
	$codeQuery=Database::query("SELECT `id`, `code`, `value`, `uses`, `level`, `duration_months` FROM codes ORDER BY id DESC");
	
	if($codeQuery->num_rows == 0){
		echo "<br /><span style='font-size:12px; color:#777; font-family:helvetica;'>
		<center>No Codes.</center></span>";
	} else {
		while($rows=$codeQuery->fetch_assoc()){
			$code=Brevada::validate($rows['code']);
			$value=Brevada::validate($rows['value']);
			$uses=Brevada::validate($rows['uses']);
			$level=Brevada::validate($rows['level']);
			$duration=Brevada::validate($rows['duration_months']);
			
			//Start Print HTML for codes
			?>
			
			<div id="financial_box" style="width:280px;">
				<div id="left" style="width:110px;">
					<strong><?php echo $code; ?></strong><br />
				</div>
				<div id="left" style="width:60px; font-size:12px; font-weight:bold;">
						<span style="color:#bc0101;"><?php echo $value; ?></span>
				</div>
				<div id="left" style="width:100px; font-size:11px;">
						<span style="color:#777;"><strong><?php echo $uses; ?></strong></span> uses, <span style="color:#777;"><?php echo $duration; ?></span> months, Level <span style="color:#777;"><?php echo $level; ?></span>.
				</div>
				<br style="clear:both;" />
			</div>
			
			<?php
			//End Print HTML for codes
		}
	}
?>
</div>

<!-- EMAIL -->
<div id="holder" class="text_clean">
	<strong>Send Support</strong><br />
	<script type='text/javascript'>
	$(function () {
		$('#em').on('submit', function (e) {
			$.ajax({
			type: 'post',
			url: 'send_support_email.php',
			data: $('form').serialize(),
			success: function () {
					alert('Email Sent');
				}
			});
			e.preventDefault();
		});
		$('#em').trigger("reset");
	});
	</script>
	<form id="em">
		<input class="inp" name="email" placeholder="Email" /> 
		<input class="button" name="submit" type="submit" value="Send Message" style="width:100px; margin:2px; box-shadow:none;" />
	</form>
	<br style="clear:both;" />
	<strong>Complete Email List</strong><br />
	<textarea class="inp" style="width:250px;"><?php
		$emailQuery=Database::query("SELECT email FROM users");
		while($row=$emailQuery->fetch_assoc()){
			echo Brevada::validate($row['email']) . ', ';
		}
	?>robbie.goldfarb@yahoo.com</textarea>
	</div>
</div>