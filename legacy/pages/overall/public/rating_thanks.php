<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/rating_thanks.css');

$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);

$user_id = @intval(Brevada::validate($_GET['u']));

$query = Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");
										
if($query->num_rows==0){
	echo '<div class="text_clean" style="padding:5px;">Invalid Page.</div>';
} else {		
	$name = ''; $url_name = '';
	while($row=$query->fetch_assoc()){
		$name = $rows['name'];
		$url_name = $rows['url_name'];				
	}
?>
<div id="holder" class="text_clean">
<div style="text-align:center; width:300px; margin:0 auto; margin-top:20px;">
<img src="/images/brevada.png" style="width:150px;"/>
<br /><br />
Thanks for rating <strong><?php echo $name; ?></strong>.<br />
To provide more feedback visit: <a href="http://brevada.com/<?php echo $url_name; ?>">brevada.com/<?php echo $url_name; ?></a>
</div>
</div>
<?php } ?>