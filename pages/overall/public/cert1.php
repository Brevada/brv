<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/cert1.css');

$user_id = empty($_SESSION['user_id']) ? Brevada::validate($_GET['u']) : $_SESSION['user_id'];

$query=Database::query("SELECT `id`, `url_name` FROM users WHERE `id`='{$user_id}' LIMIT 1");

$url_name = '';
while($row = $query->fetch_assoc()){
   $url_name = $row['url_name'];
}
?>
<a href="/">
<img src="/images/cert1.png"  style="height:200px;"/>
</a>
<br style="clear:both;" />
<div style="margin-top:-182px; margin-left:34px; line-height:37px; font-size:37px; font-weight:bold; color:#ee2b2b; font-family:helvetica;">
<?php
$running=0; $count=0;
$query = Database::query("SELECT `value`, `user_id` FROM feedback WHERE user_id='{$user_id}'");
while($row = $query->fetch_assoc()){
	$running += @intval($row['value']);
	$count++;
}

if($count > 0){
	echo number_format($running/$count) . '%';
} else {
	echo 'unknown percent';
}
?>
<br />
<span style="font-size:11px; line-height:12px; font-weight:normal;"><?php echo $count; ?> ratings</span>
</div>
<a href="http://brevada.com/overall/public/verify.php?v=<?php echo $user_id*77777; ?>">
<div style="margin-top:135px; font-size:12px; color:#ee2b2b; font-family:helvetica;">
Verify
</div>
</a>