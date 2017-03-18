<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/corporate_profile.css');

$url_name = Brevada::validate($_GET['name'], VALIDATE_DATABASE);

$query = Database::query("SELECT `active`, `url_name`, `id` FROM users WHERE url_name='{$url_name}' AND active='yes' LIMIT 1");
if($query !== false && $query->num_rows > 0){
	$row = $query->fetch_assoc();
	if($row !== false){
		$id = Brevada::validate($row['id']);
	}
}

//GET COUNTRY
$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$this->add(new View('../widgets/corporate/corporate_profile_header.php'));
?>

<br style="clear:both;" />
<div  style="width:100%; margin-top:5px;">
	<div id="sized_container">	
	<?php
	$query = Database::query("SELECT `corp_id`, `user_id`, `id` FROM `corporate_links` WHERE `corp_id` = '{$id}' ORDER BY `id` DESC");

	if($query->num_rows == 0){
		echo "This corporate account has no associated sub-accounts.";
	} else {
		while($row=$query->fetch_assoc()){
		   $company_id = $row['user_id'];
		   $this->add(new View('../widgets/corporate/corporate_company_display.php', array('company_id' => $company_id)));
		}
	}
	?>
	</div>
</div>
<?php $this->add(new View('../template/footer.php')); ?>