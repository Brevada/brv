<?php
$this->IsScript = true;

$tablet_id = '';
$tablet_url = '';

$serial = Brevada::validate(Brevada::FromPOSTGET('m'), VALIDATE_DATABASE);

if(!empty($serial)){
	if(($query = Database::query("SELECT stores.URLName, stores.id as SID FROM `tablets` LEFT JOIN stores ON stores.id = tablets.StoreID WHERE tablets.`SerialCode` = '{$serial}' LIMIT 1")) !== false){
		$row = $query->fetch_assoc();
		if($row && !empty($row)){
			$tablet_id = $row['SID'];
			$tablet_url = $row['URLName'];
		} else {
			Database::query("INSERT INTO `tablets` (SerialCode, StoreID, Status) VALUES ('{$serial}', NULL, 'Pending')");
			exit("Error: Request pending with ID: {$serial}.");
		}
	} else {
		exit;
	}
} else { exit; }

$id = @intval(Brevada::validate($tablet_id));
$url_name=Brevada::validate($tablet_url, VALIDATE_DATABASE);

$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$condition = empty($url_name) ? "id = '".$_SESSION['StoreID']."'" : "URLName='{$url_name}'";

$query = Database::query("SELECT * FROM `stores` WHERE {$condition} LIMIT 1");

if($query->num_rows == 0) {
	Brevada::Redirect('/404');
}

$_SESSION['SessionCode'] = strval(bin2hex(openssl_random_pseudo_bytes(16)));

$store_id='';
$name='';
$user_extension='';
$corporate='';

while($row=$query->fetch_assoc()){
   $store_id=Brevada::validate($row['id']);
   $name=Brevada::validate($row['Name']);
   $url_name=Brevada::validate($row['URLName']);
}
?>

	
<div class="topbar">
	<div class="container">
		<div class="title">
			<?php echo sprintf(__('Give %s Feedback'), "<b>{$name}</b>"); ?>
		</div>
		<div class="icons"><img class="logo" src="/images/quote.png" /></div>
	</div>
</div>
<div class='push'></div>

<div id="aspects" class="aspect-container container">
	<?php
	$postQuery=Database::query("SELECT aspects.ID, aspect_type.Title, aspect_type.Description as Description FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.StoreID = {$store_id} AND aspects.`Active` = 1 ORDER BY aspects.ID DESC");
	if($postQuery !== false && $postQuery->num_rows > 0){
		while($row=$postQuery->fetch_assoc()) {		
			$this->add(new View('../widgets/profile/post_box.php', array('row' => $row, 'id' => $store_id)));
		}
	}
	?>	
	<div class="bottom-spacer"></div>
</div>

<div class="fixed-toolbar">
	<div class="container">
		<div class="submit" id='imdone'><i class="fa fa-check"></i> <?php _e("I'm Done"); ?></div>
	</div>
</div>

<div id="email_connect"  class="aspect-container container" style="display: none;">
	<?php $this->add(new View('../widgets/profile/email_connect.php', array('store_id' => $store_id))); ?>
</div>