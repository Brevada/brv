<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/promo_white.css');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/logout.php');
}

$store_id = $_SESSION['StoreID'];
$query = Database::query("SELECT URLName FROM `stores` WHERE `id` = '{$store_id}' LIMIT 1");

if($query->num_rows==0){
	Brevada::Redirect('/logout.php');
}

$url_name = '';
while($row=$query->fetch_assoc()){
	$url_name = $row['URLName'];
}
?>
<img src="/images/promo_square.png"  style="width:400px;"/>
<div style="width:400px; text-align:center; margin-top:-425px; color:#ee2b2b; font-family:tahoma; font-size:50px;">
	<img id="company_pic" src="/user_data/user_images/default.png"  />
</div>
<div style="width:400px; text-align:center; margin-top:80px; color:#ee2b2b; font-family:tahoma; font-size:50px;">
	<img src="/qr/<?php echo $url_name; ?>.png" height="120px" />
</div>
<div style="width:400px; text-align:center; margin-top:65px; color:#fff; font-family:tahoma; font-size:20px;">
	brevada.com/<?php echo $url_name; ?>
</div>
<script type='text/javascript'>
$(document).ready(function(){
	window.print();
});
</script>