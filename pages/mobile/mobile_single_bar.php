<?php
$this->addResource('/css/mobile/mobile_single_bar.css');
$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);

$post_id = Brevada::FromPOSTGET('id');

//GET COUNTRY
$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];


$query=Database::query("SELECT * FROM posts WHERE id='{$post_id}' LIMIT 1");
if($query->num_rows==0){
	//No user exists
}
$id = ''; $post_id = ''; $user_id = ''; $name = ''; $post_name = ''; $name = '';
$post_name = ''; $type = ''; $post_extension = '';
while($rows=$query->fetch_assoc()){
   $id=$rows['id'];
   $post_id=$id;
   $user_id=$rows['user_id'];
   $name = $rows['name'];
   $post_name=$name;
   $type = $rows['type'];
   $post_extension=$rows['extension'];
   
}
?>

<?php
$query=Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");
if($query->num_rows==0){
	//No user exists
}
$user_name = ''; $user_extension = '';
while($row=$query->fetch_assoc()){
   $user_name=$rows['name'];
   $user_extension = $rows['extension'];
   
}

					$ip=$_SERVER["REMOTE_ADDR"];
					$check=Database::query("SELECT * FROM feedback WHERE ip_addres = '{$ip}' AND post_id='{$post_id}' ORDER BY id DESC");
  				    $check_rate=$check->num_rows;

?>


<div id="cont">
	<div id="pod">
		<div id="bg_holder" style="height:170px; width:100%; background:url('user_images/<?php echo $user_id; ?>.<?php echo $user_extension; ?>'); background-size:100%;">
			
		</div>
		
		<div id="top_holder" style="height:170px; margin-top:-170px; background:rgba(0,0,0,0.7);">
			
			<div id="pic_holder">
			<?php $this->add(new View('../pages/mobile/show_user_image.php', array('user_id' => $user_id, 'post_id' => $post_id, 'post_extension' => $post_extension))); ?>
			</div>
			
			<div id="title_holder">
			<strong><?php echo $name; ?></strong>
			<br />
			<?php echo $user_name; ?>
			<br />
			<div class="button" id="message_button" onclick="message_show()" style="display:none;">Message</div>
			</div>
		</div>
		
		<div id="message_box" align="left" style="display:none; width:100%;">
		<?php $this->add(new View('../pages/mobile/post_message.php', array('post_id' => $post_id, 'name' => $name))); ?>
		</div>
		
		
		<script>
function message_show() {
	document.getElementById("message_box").style.display='block';
	document.getElementById("message_button").style.display='none';
}
</script>
		
		<div style="margin-top:0px;">
		
		<?php $this->add(new View('../pages/mobile/rater2.php', array('post_id' => $post_id))); ?>
			
			<div id="text_holder">		
				<?php $this->add(new View('../pages/mobile/post_comment_mobile.php', array('post_id' => $post_id))); ?>
			</div>
		</div>
		
		<?php $this->add(new View('../pages/mobile/widget_bottom.php')); ?>
	
	</div>
	
</div>