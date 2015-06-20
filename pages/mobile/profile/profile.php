<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/mobile/profile.css');
$this->addResource('/js/mobile_profile.js');

$id = @intval(Brevada::validate($_POST['id']));
$url_name=Brevada::validate($_GET['name'], VALIDATE_DATABASE);

$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);

$query = Database::query("SELECT * FROM users WHERE url_name='{$url_name}' LIMIT 1");

if($query->num_rows == 0) {
	Brevada::Redirect('/profile/not_found.php');
}

$id='';
$user_id='';
$name='';
$type='';
$user_extension='';
$corporate='';

while($row=$query->fetch_assoc()){
   $id=Brevada::validate($row['id']);
   $user_id=$id;
   $name=Brevada::validate($row['name']);
   $type=Brevada::validate($row['type']);
   $user_extension=Brevada::validate($row['extension']);
   $corporate=Brevada::validate($row['corporate']);
}

if($corporate == '1'){
	Brevada::Redirect("/corporate/profile/corporate_profile.php?name={$url_name}");
}

$this->setTitle("Give {$name} Feedback");

$this->addResource("<meta property='og:title' content='Give us feedback!'/>", true, true);
$this->addResource("<meta property='og:type' content='website'/>", true, true);
$this->addResource("<meta property='og:url' content='http://brevada.com/{$url_name}'/>", true, true);
$this->addResource("<meta property='og:image' content='http://brevada.com/images/square_logo.png'/>", true, true);
$this->addResource("<meta property='og:site_name' content='Brevada'/>", true, true);
$this->addResource("<meta property='og:description' content='Give {$name} Feedback on Brevada'/>", true, true);
?>

<div id="top_bar">
	<div class="container">
	 <img id="logo" src="/images/quote.png" />
    </div>
</div>

<div id="info_bar">
	<div id="user_logo"><?php post_pic('100px','auto','0', $user_id, 'NONE', $user_extension); ?></div>
	<div id="name"><?php echo $name; ?></div>
	<div id="description"><?php echo $type; ?></div>
	<div id="buttons">
		<div class="button4" id="suggestions_button">Suggestions</div>
	</div>       
	<div id="suggestion_box" style="display:none;">
		<form  action="/overall/insert/insert_message.php"  method="post">
			<input type="hidden" name="userid" id="ipaddress<?php echo $post_id; ?>" value="<?php echo $user_id; ?>" />
			<textarea class="inp" id="suggestion" placeholder="General suggestions or comments" class="ta" style="width:100%;"></textarea>
			<div class="button4" onclick="SubmitFormSuggestion('<?php echo $user_id; ?>'), close_suggestion(), thanks_suggestion();" style="width:100%; height:30px; line-height:30px;">Submit Suggestions</div>
		</form>
    </div> 
</div>

<div  style="width:100%; margin: 0 auto; margin-top:0px;  padding-top:0px;">
	<div id="sized_containerHub">
 	
 	<div style="margin: 5px auto auto 0px; overflow:hidden;">
	<?php
	$reviewer=Brevada::validate(empty($_GET['reviewer']) ? '' : $_GET['reviewer']);
	$postQuery=Database::query("SELECT * FROM posts WHERE user_id = '{$id}' AND active='yes' ORDER BY id DESC");
	if($postQuery !== false && $postQuery->num_rows > 0){
		while($row=$postQuery->fetch_assoc()) {		
			$this->add(new View('../widgets/profile/post_box.php', array('row' => $row, 'reviewer' => $reviewer, 'country' => $country, 'ip' => $ip, 'id' => $user_id, 'user_extension' => $user_extension, 'mobile' => 'true')));
		}
	}
	?>	
	</div>
</div>
</div>
<?php $this->add(new View('../template/footer.php')); ?>