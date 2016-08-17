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
	Brevada::Redirect('/api/setup');
}

$store_id='';
$name='';
$user_extension='';
$corporate='';

while($row=$query->fetch_assoc()){
   $store_id=Brevada::validate($row['id']);
   $name=Brevada::validate($row['Name']);
   $url_name=Brevada::validate($row['URLName']);
}

$welcome_message = '';
$allow_comments = false;

if (($stmt = Database::prepare("
	SELECT WelcomeMessage, AllowComments FROM store_features
	JOIN stores ON stores.FeaturesID = store_features.id
	WHERE stores.id = ?
")) !== false){
	$stmt->bind_param('i', $store_id);
	if(!$stmt->execute()){
		$message = "Unknown error. 500.";
	} else {
		$stmt->bind_result($welcome_message, $allow_comments);
		if($lookup_result = $stmt->fetch()){
			$welcome_message = empty($welcome_message) ? '' : $welcome_message;
			$allow_comments = $allow_comments == 1;
		}
	}
	$stmt->close();
}
?>

<div class="topbar profile-topbar">
	<div class="container">
		<img class="logo" src="/images/quote.png" />
		<div class="title<?= strlen($welcome_message) > 0 ? ' custom-title' : ''; ?>">
			<span class='full-message'><?php
				if (strlen($welcome_message) > 0){
					echo nl2br(str_replace(['%store%'], [$name], $welcome_message));
				} else {
					echo sprintf(__('Give %s Feedback'), "<b>{$name}</b>");
				}
			?></span>
			<span class='shortened-message'><?php echo sprintf(__('Give %s Feedback'), "<b>{$name}</b>"); ?></span>
		</div>
		<i class="fa fa-arrow-circle-down"></i>
	</div>
</div>
<div class="top-spacer"></div>

<div id="aspects" class="aspect-container container">
	<?php
	$postQuery=Database::query("SELECT aspects.ID, aspect_type.Title, aspect_type.Description as Description FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.StoreID = {$store_id} AND aspects.`Active` = 1 ORDER BY aspect_type.Title");
	if($postQuery !== false && $postQuery->num_rows > 0){
		while($row=$postQuery->fetch_assoc()) {		
			$this->add(new View('../widgets/profile/post_box.php', array('row' => $row, 'id' => $store_id, 'tablet' => true)));
		}
	}
	?>	
	<div class="bottom-spacer"></div>
</div>

<div class="fixed-toolbar">
	<div class="container-fluid">
		<div class="submit" id='imdone'><i class="fa fa-check"></i> <?php _e("I'm Done"); ?></div>
	</div>
</div>

<div id="email_connect"  class="aspect-container container" style="display: none;">
	<div class="thanks-header">
		<h1><?php _e("Thanks for the feedback!"); ?></h1> 
	</div>

	<div id="reset" class="refresh">
		<i class="fa fa-refresh"></i>
	</div>
</div>

<?php $this->add(new View('../widgets/profile/data_collection.php', array('store_id' => $store_id, 'tablet' => true))); ?>
<?php if ($allow_comments){ $this->add(new View('../widgets/profile/comments.php', array('store_id' => $store_id, 'tablet' => true))); } ?>