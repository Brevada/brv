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
<img src="/images/promo_rectangleWhite.png" style="height:350px;"/>
<div style="margin-left:440px; margin-top:-190px; color:#ee2b2b; font-family:tahoma; font-size:50px;"><?php echo $url_name; ?></div>
<div style="margin-left:20px; margin-top:-130px; color:#cd0000; font-family:tahoma; font-size:40px;">
	<img src="/user_data/qr/<?php echo $user_id; ?>.png" height="120px" />
</div>
<div style="width:980px; text-align:center; margin-top:30px; color:#ee2b2b; font-family:tahoma; font-size:50px;">
	<?php  if($user_extension=="none"){ ?>
	<img id="company_pic" src="/user_data/user_images/default.png"  />
	<?php  } else { ?>
	<img id="company_pic" src="/user_data/user_images/<?php  echo $user_id; ?>.<?php  echo $user_extension; ?>"  />
    <?php  } ?>
</div>