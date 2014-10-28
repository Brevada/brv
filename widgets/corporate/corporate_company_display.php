<?php
$this->addResource('/css/corporate_company_display.css');

$company_id = $this->getParameter('company_id');

$queryComp = Database::query("SELECT * FROM users WHERE id='{$company_id}'");

$company_name = ''; $company_email = ''; $company_url_name = ''; $company_active = '';
$company_logins = ''; $company_extension = ''; $company_type = ''; $company_trial = '';
$company_picture = ''; $company_expiry_date = ''; $company_user_extension = '';
$company_corporate = '';

while($rowsComp=$queryComp->fetch_assoc()){
	$company_name=$rowsComp['name'];
	$company_email=$rowsComp['email'];
	$company_url_name=$rowsComp['url_name'];
	$company_active=$rowsComp['active'];
	$company_logins=$rowsComp['logins'];
	$company_extension=$rowsComp['extension'];
	$company_type=$rowsComp['type'];
	$company_trial=$rowsComp['trial'];
	$company_picture=$rowsComp['picture'];
	$company_expiry_date=$rowsComp['expiry_date'];
	$company_user_extension=$rowsComp['extension'];
	$company_corporate=$rowsComp['corporate'];
?>
<div id="company_box">
	<?php user_pic('400px', '', $company_id, $company_extension); ?>
</div>
<div id="company_box_content">
	<div class="company_box_left">
		<?php echo $company_name; ?><br />
		<span style="font-size:11px;">brevada.com/<?php echo $company_url_name; ?></span>
	</div>
	<div class="company_box_left" style="float:right;">
		<a href="http://brevada.com/<?php echo $company_url_name; ?>"><div class="button4" style="margin-top:3px;">Give Feedback</div></a>
	</div>
</div>
<?php } ?>