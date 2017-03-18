<?php
$this->addResource('/css/corporate_profile_header.css');

$url_name = Brevada::validate($_GET['name']);

$query = Database::query("SELECT * FROM users WHERE url_name='{$url_name}' AND active='yes' LIMIT 1");

if($query === false || $query->num_rows == 0){
	Brevada::Redirect('/404');
}

$id = ''; $name = ''; $type = ''; $user_extension = '';

while($row = $query->fetch_assoc()){
   $id = $row['id'];
   $name = $row['name'];
   $type = $row['type'];
   $user_extension = $row['extension'];
}
  
$this->setTitle("{$name} - Brevada Feedback");
?>
<div  style="width:100%; margin-top:0px; height:110px; padding-top:18px; background:url('/user_data/user_images/<?php echo $id; ?>.<?php echo $user_extension; ?>'); background-size:100%;"></div>
<div  style="width:100%; margin-top:-128px; height:110px; padding-top:18px; background:rgba(0,0,0,0.7);">
		<div id="sized_container" style="padding-left:0px;">	
			<div align="center" style="width:400px; float:left;">
				<div align="left" style="float:left; width:150px; overflow:hidden;">
					<?php  if($user_extension=="none"){ ?>
					<img id="banner_pic" src="/user_data/user_images/default.jpg"  />
					<?php  } else { ?>
					<img id="banner_pic" src="/user_data/user_images/<?php  echo $id; ?>.<?php  echo $user_extension; ?>"  />
					<?php  } ?>
				</div>
				<div  align="left" style="float:left; margin-left:10px;">
				<p class="h1_light" style="margin-left:5px; color:#f9f9f9; width:210px; font-size:17px; overflow:hidden;"><?php echo $name; ?></p><br />
				</div>
				<div id="message_box" align="left" style="display:none;float:left; width:130px; margin-left:10px;">
					<?php
					/* This file doesn't exist.
					include'../post_message_mobile.php';*/
					?>
				</div>
				<div id="help_box" align="left" style="display:none;float:left; width:300px; margin-left:10px; color:#555555; font-size:11px;">
					Give feedback on a company below by clicking 'Give Feedback'. <a href="mailto:contact@brevada.com">More help.</a>
				</div>
			</div>
		</div>	
</div>
<div id="helper" class="dis">
	Give feedback on a company below by clicking 'Give Feedback'.
</div>
<script type='text/javascript'>
$(document).ready(function(){
    setTimeout(function(){ $(".dis").fadeOut(); }, 3000);
    $("#needhelp").click(function() { 
    $("#helper").fadeIn("slow");
  });
});

function message_show() {
	$("#message_box").show();
}
function help_show() {
	$("#help_box").show();
}
</script>