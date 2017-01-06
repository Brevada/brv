<?php
$this->add(new View('../template/home_header.php'));
$this->addResource('/css/verify.css');

$user_id = Brevada::validate($_GET['v'])/77777; 

$query = Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");
if($query->num_rows==0){
	Brevada::Redirect('/');
}

$name = 'Unknown';
while($row = $query->fetch_assoc()){
   $name = $row['name'];
}

$running=0;
$query=Database::query("SELECT `user_id`, `value` FROM feedback WHERE user_id='{$user_id}'");
while($row=$query->fetch_assoc()){
	$running += @intval($row['value']);
}
$count = $query->num_rows;
?>
<br style="clear:both;" />
<div id="v_holder">
Verified: <strong><?php echo $count > 0 ? number_format($running/$count) . '%' : 'unknown percent'; ?></strong> in <strong><?php echo $count; ?></strong> ratings for <?php echo $name; ?>.
</div>