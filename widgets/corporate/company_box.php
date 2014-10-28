<?php
$this->addResource('/css/company_box.css');
$this->addResource('/js/company_box.js');

$company_id = $this->getParameter('company_id');

$query = Database::query("SELECT * FROM users WHERE id='{$company_id}' LIMIT 1");

$company_name = ''; $company_email = ''; $company_url_name = ''; $company_active = ''; $company_logins = ''; $company_extension = ''; $company_type = ''; $company_trial = ''; $company_picture = ''; $company_expiry_date = ''; $company_user_extension = ''; $company_corporate = '';

while ($row = $query->fetch_assoc()){
	$company_name = $row['name'];
	$company_email = $row['email'];
	$company_url_name = $row['url_name'];
	$company_active = $row['active'];
	$company_logins = $row['logins'];
	$company_extension = $row['extension'];
	$company_type = $row['type'];
	$company_trial = $row['trial'];
	$company_picture = $row['picture'];
	$company_expiry_date = $row['expiry_date'];
	$company_user_extension = $row['extension'];
	$company_corporate = $row['corporate'];
}
?>

<div class="company_box"><?php user_pic('720px', '',$company_id, $company_extension); ?></div>
<div class="company_box_content">
	<div class="company_box_left" style="width:190px;">
		<strong><span style="font-size:14px;"><?php echo $company_name; ?></span></strong>
		<br />brevada.com/<?php echo $company_url_name; ?><br />		
		<?php 
			//Account Status
			if($company_trial == 1){
				echo "<div class='company_box_trial'>Trial</div>";
			}
			else if($company_active != 'yes'){
				echo "<div class='company_box_inactive'>Inactive</div>";
			}
			else {
				echo "<div class='company_box_active'>Active</div>";
			}
		?>
	</div>
	<div class="company_box_left" style="width:200px;">
		<!-- RATINGS AVERAGE -->
		<?php
		//Calculate Average
		$query2 = Database::query("SELECT * FROM feedback WHERE user_id='{$company_id}'");
		
		$total = 0;
		
		if($query2->num_rows > 0) {
			while($row = $query2->fetch_assoc()){
				$total += intval($row['value']);
			}
		}
		
		$average = 0;
		
		if($query2->num_rows > 0){
			$average = round($total / $query2->num_rows, 2);
		}

		$color = 'red';
		if($average >= 80) {
			$color = 'green';
		} else if($average >= 60) {
			$color = 'orange';
		}
		?>
		<div class="company_box_number">
			<span class="one_rating" style="color:<?php echo $color; ?>;"><?php echo $average; ?></span>
			<span style="font-size:10px;">average in  <?php echo $query2->num_rows; ?> ratings.</span>
		</div>
		
		<!-- COMMENTS -->
		<?php
		$comment_count = Database::query("SELECT `user_id` FROM comments WHERE user_id='{$company_id}'")->num_rows;
		?>
		<div class="company_box_number"><?php echo $comment_count; ?>&nbsp;<span style="font-size:10px;">total comments.</span>
		</div>
	</div>
	
	<div class="company_box_left" style="float:right;">
		<form action="/corporate/hub/corporate_login.php" method="POST">
			<input type="hidden" name="user_id" value="<?php echo $company_id; ?>" />
			<input class="button4" value="Login" type="submit" style="width:122px;" />
		</form>
		<a class="corpHead" companyid="<?php echo $company_id; ?>" data-reveal-id="unlock">
			<div class="button4" style="width:100px; text-align:center; opacity:0.8;"><span id="corpSign<?php echo $company_id; ?>">View</span> Data</div>
		</a>
	</div>
	<br style="clear:both;" />
</div>
		
<div id="corpContent<?php echo $company_id; ?>" style="display:none; margin-top:0px; padding:0px; width:718px;  border:0px solid #dcdcdc; border-top:0px; ">
	<?php $this->add(new View('../widgets/corporate/company_box_data.php', array('company_id' => $company_id))); ?>	
</div>