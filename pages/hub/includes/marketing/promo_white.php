<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/promo_white.css');

$user_id = $_SESSION['user_id'];
$query = Database::query("SELECT `id`, `url_name`, `extension` FROM `users` WHERE `id` = '{$user_id}' LIMIT 1");

if($query->num_rows==0){
	Brevada::Redirect('/home/logout.php');
}

$url_name = '';
$user_extension = '';
while($row=$query->fetch_assoc()){
	$url_name = $row['url_name'];
	$user_extension = $row['extension'];
}
?>
<script type='text/javascript'>
$(document).ready(function(){
	window.print();
});
</script>
<img src="/images/promo_square.png"  style="width:400px;"/>
<div style="width:400px; text-align:center; margin-top:-425px; color:#ee2b2b; font-family:tahoma; font-size:50px;">
	<img id="company_pic" src="/user_data/user_images/default.png"  />
</div>
<div style="width:400px; text-align:center; margin-top:80px; color:#ee2b2b; font-family:tahoma; font-size:50px;">
	<img src="/user_data/qr/<?php echo $user_id; ?>.png" height="120px" />
</div>
<div style="width:400px; text-align:center; margin-top:55px; color:#fff; font-family:tahoma; font-size:20px;">
	brevada.com/<?php echo $url_name; ?>
</div>

