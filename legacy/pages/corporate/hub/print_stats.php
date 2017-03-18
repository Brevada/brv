<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/print_stats.css');

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$queryCorp = Database::query("SELECT `corp_id`, `id`, `user_id` FROM corporate_links WHERE corp_id='{$user_id}' ORDER BY `id` DESC");

$company_id='';

if($queryCorp->num_rows == 0){
	echo "No associated sub-accounts.";
} else {
	while($rowCorp=$queryCorp->fetch_assoc()){
		$company_id = $rowCorp['user_id'];
		
		$queryComp=Database::query("SELECT `id`, `name`, `email` FROM users WHERE id='{$company_id}' ORDER BY `id` DESC");
		while($rowComp=$queryComp->fetch_assoc()){
			$user_id=$rowComp['id'];
			$user_name=$rowComp['name'];
			$user_email=$rowComp['email'];
?>
			
<div id="stats_box">
	<div class="stats_left" style="font-size:15px;">	
		<strong><?php echo $user_name; ?></strong><span style="font-size:11px;"><br /><?php echo $user_email; ?></span>
	</div>
	<div class="stats_left" style="float:right;">
	<?php
	//Calculate Average
	$query2=Database::query("SELECT * FROM feedback WHERE user_id='{$company_id}'");
	
	$total=0;
	
	if($query2->num_rows > 0) {
		while($row=$query2->fetch_assoc()){
			$total += intval($row['value']);
		}
	}
	
	$average=0;
	
	if($query2->num_rows > 0){
		$average=round($total / $query2->num_rows, 2);
	}

	$color='red';
	if($average >= 80) {
		$color='green';
	} else if($average >= 60) {
		$color='orange';
	}
	?>
	<span class="one_rating" style="color:<?php echo $color; ?>;">
		<span style="font-size:18px;"><?php echo $average; ?></span>
	</span>
	<span style="font-size:12px;">average in  <strong><?php echo $query2->num_rows; ?></strong> ratings.</span>
	</div>
	<br style="clear:both;" />
	<div style="margin-top:5px;"><?php $this->add(new View('../widgets/corporate/company_box_data.php', array('company_id' => $company_id))); ?></div>
</div>
			<?php
		}
	}
}
?>